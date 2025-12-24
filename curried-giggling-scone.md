# Implementierungsplan: Minimal Contact Form v1.0.0 - UX Redesign

## Übersicht

Komplettes UX Redesign mit vereinfachter Feldkonfiguration (field groups statt drag & drop), Theme-System mit separaten CSS-Dateien, full-featured CSS Editor mit CodeMirror 6, erweiterte Sicherheit (Honeypot, Time-Check, CSRF, Rate Limiting) und vollständige SMTP-Konfiguration.

---

## Phase 1: Vereinfachte Feldkonfiguration (Field Groups)

### Ziel
Ersetze das komplexe Drag & Drop System durch einfache Field Groups mit Mode Toggles.

### 1.1 TypeScript Types

**Datei:** `admin-app/src/types.ts`

**Änderungen:**
```typescript
export interface MCFFieldConfig {
    field_groups: {
        company: { enabled: boolean };
        name: { mode: 'single' | 'split'; enabled: boolean };
        contact: { mode: 'email' | 'email-phone'; enabled: boolean };
        subject: { enabled: boolean };
        message: { enabled: boolean };
        gdpr: { enabled: boolean };
        submit: { alignment: 'left' | 'right' };
    };
    labels: Record<string, string>;
    placeholders: Record<string, string>;
}

// ENTFERNEN: order, enabled, widths, custom_fields
```

### 1.2 Migration Logic

**Datei:** `src/Core/Migration.php`

**Neue Methode hinzufügen:**
```php
private static function migrate_to_field_groups($old_fields)
{
    $enabled = $old_fields['enabled'] ?? [];

    // Detect name mode
    $name_mode = 'split';
    if (isset($enabled['name']) && $enabled['name']) {
        $name_mode = 'single';
    }

    // Detect contact mode
    $contact_mode = 'email';
    if (isset($enabled['phone']) && $enabled['phone']) {
        $contact_mode = 'email-phone';
    }

    return [
        'field_groups' => [
            'company' => ['enabled' => $enabled['company'] ?? false],
            'name' => ['mode' => $name_mode, 'enabled' => true],
            'contact' => ['mode' => $contact_mode, 'enabled' => true],
            'subject' => ['enabled' => $enabled['subject'] ?? true],
            'message' => ['enabled' => true],
            'gdpr' => ['enabled' => true],
            'submit' => ['alignment' => 'left'],
        ],
        'labels' => $old_fields['labels'] ?? self::get_default_labels(),
        'placeholders' => $old_fields['placeholders'] ?? [],
    ];
}
```

### 1.3 React Component: FieldGroupManager (MIT Live-Vorschau!)

**WICHTIG:** Dieser Component IST die Live-Vorschau! Zeigt Formularfelder inline an, wie der aktuelle FieldManager.

**Datei:** `admin-app/src/components/FieldGroupManager.tsx` (NEU)

**Struktur:**
- SimpleToggleGroup für Company, Subject
- ModeToggleGroup für Name (single/split), Contact (email/email-phone)
- SubmitGroup für Alignment (left/right)
- **Inline Formular-Preview** mit Readonly-Feldern (wie aktueller FieldManager)
- Label/Placeholder Editing per Modal oder Inline-Controls

### 1.4 Dependencies entfernen

**Datei:** `admin-app/package.json`

```bash
npm uninstall @dnd-kit/core @dnd-kit/sortable @dnd-kit/utilities
```

### 1.5 Alte Komponente löschen

**Datei:** `admin-app/src/components/FieldManager.tsx` → **LÖSCHEN**

### 1.6 Backend Updates

**Dateien:**
- `src/Core/Activator.php` - Default options mit field_groups Struktur
- `src/Models/FieldConfig.php` - Validation für field_groups
- `src/Public/FormRenderer.php` (NEU) - Form Rendering basierend auf field_groups (ersetzt Legacy)

---

## Phase 2: Theme-System mit CSS-Dateien

### Ziel
Jede Theme-CSS-Datei enthält BEIDE Varianten (light/dark) via `[data-theme-variant]` Selektoren, plus Primary Color Override.

### 2.1 TypeScript Types

**Datei:** `admin-app/src/types.ts`

```typescript
export interface MCFStyling {
    theme_preset: 'modern' | 'minimal';
    variant: 'light' | 'dark';
    primary_color?: string;
    custom_css?: string;
}
```

### 2.2 CSS-Dateien erstellen

**Verzeichnis:** `assets/public/css/themes/`

**2 Dateien erstellen:**
1. `modern.css` - Enthält `.mcf-form[data-theme-variant="light"]` UND `[data-theme-variant="dark"]`
2. `minimal.css` - Gleiche Struktur

Jede Datei definiert ALLE 28 MCF CSS-Variablen für beide Varianten.

### 2.3 Frontend Loading

**Datei:** `src/Public/Frontend.php`

**Enqueue-Logik:**
1. Base-Styles: `style.css` (Struktur, Layout)
2. Theme-Datei: `themes/{theme_preset}.css` (enthält beide Varianten)
3. Inline Override: Primary Color (falls gesetzt)
4. Inline Custom CSS (falls gesetzt)

### 2.4 Form Rendering mit Data-Attribut

**Datei:** `src/Public/FormRenderer.php` (NEU - ersetzt Legacy)

```php
<div class="mcf-form" data-theme-variant="<?php echo esc_attr($variant); ?>">
```

**Vollständige neue Klasse mit:**
- Shortcode Registration `[minimal_contact_form]`
- Form HTML Rendering basierend auf field_groups
- Field Helper Methods (add_input, add_textarea, etc.)
- Security Fields (honeypot, CSRF, timestamp)
- GDPR Privacy Text Rendering

### 2.5 Theme Selector Update

**Datei:** `admin-app/src/components/ThemeSelector.tsx`

- RadioControl für Theme (modern, minimal)
- RadioControl für Variant (light, dark)
- ColorPicker für Primary Color Override

### 2.6 Migration Enhancement

**Datei:** `src/Core/Migration.php`

Mapping alter Themes zu neuen:
- `light` → `modern` + `light`
- `dark` → `modern` + `dark`
- `minimal` → `minimal` + `light`

Primary Color aus altem `button-background-color` extrahieren.

---

## Phase 3: CSS Editor mit CodeMirror 6

### Ziel
Full-featured CSS Editor mit Autocomplete. Custom CSS wird direkt im FieldGroupManager (Live-Vorschau) angezeigt!

### 3.1 Dependencies installieren

```bash
cd admin-app
npm install @codemirror/state @codemirror/view @codemirror/lang-css \
            @codemirror/autocomplete @codemirror/commands @codemirror/search \
            @codemirror/basic-setup
```

### 3.2 CSS Editor Component

**Datei:** `admin-app/src/components/CSSEditor.tsx` (NEU)

- EditorView mit basicSetup
- CSS Language Support
- Autocomplete für alle 28 MCF-Variablen (`--mcf-*`)
- onChange Handler (gibt CSS-String zurück)

### 3.3 FieldGroupManager Enhancement (Custom CSS Injection)

**WICHTIG:** Der FieldGroupManager zeigt ALLES: Felder UND Custom CSS Styling!

**Datei:** `admin-app/src/components/FieldGroupManager.tsx`

**Zusätzliche Props:**
- `customCSS: string` - Custom CSS von styling.custom_css
- `theme: string` - Theme preset
- `variant: string` - Light/Dark variant

**Rendering:**
```jsx
<div className="mcf-field-preview">
  <style>{customCSS}</style>  {/* Custom CSS inline injizieren! */}
  <div className="mcf-form" data-theme-variant={variant}>
    {/* Formular-Felder basierend auf field_groups */}
  </div>
</div>
```

**Debouncing:** Custom CSS Update mit 300ms Debounce für Performance.

### 3.4 Advanced Styling Component Update

**Datei:** `admin-app/src/components/AdvancedStyling.tsx`

Ersetze Placeholder mit:
- CSSEditor Component (nur Editor, keine Preview!)
- Hilfetext mit MCF-Variablen
- onChange direkt zu `onStylingChange('custom_css', value)`

**KEIN** 2-Spalten Layout! Nur der Editor. Preview ist im FieldGroupManager.

### 3.5 App.tsx Update

**Datei:** `admin-app/src/App.tsx`

FieldGroupManager bekommt zusätzliche Props:
```tsx
<FieldGroupManager
    fields={settings.fields}
    customCSS={settings.styling.custom_css || ''}
    theme={settings.styling.theme_preset}
    variant={settings.styling.variant}
    onFieldsChange={(fields) => setSettings({ ...settings, fields })}
/>
```

### 3.6 Styling

**Datei:** `admin-app/src/admin.scss`

Styling für CSSEditor (ohne Grid/Preview-Panel, da Preview im FieldGroupManager ist).

---

## Phase 4: Erweiterte Sicherheit (ALLE 4 Features)

### Ziel
Implementiere ALLE Sicherheitsfeatures in der NEUEN PSR-4 Struktur (nicht Legacy!).

### 4.1 Neuer FormHandler (Vollständig)

**Datei:** `src/Public/FormHandler.php`

**Ersetze Wrapper mit voller Implementation:**

**REST Route:** `POST /mcf/v1/submit`

**Validierungen (in Reihenfolge):**
1. **Honeypot Check** - 3 Felder (website, url, business_email) müssen leer sein
2. **Time-based Check** - Timestamp + Nonce, 3s minimum, 1h maximum
3. **CSRF Token** - Session-basiert, hash_equals Vergleich
4. **Rate Limiting** - IP-basiert, max 3 Submissions/10min (Transients)
5. **Field Validation** - Email, Message, GDPR (falls optin)
6. **Email Sending** - (Phase 5)

**Methoden:**
- `validate_honeypot($data)`
- `validate_time_check($data)`
- `validate_csrf($data)`
- `validate_rate_limit()`
- `record_submission()`
- `get_client_ip()`

### 4.2 Form Security Fields

**Datei:** `src/Public/FormRenderer.php`

**Neue `add_security()` Methode:**
```php
// CSRF Token (session)
$_SESSION['mcf_csrf_token'] = bin2hex(random_bytes(32));

// Timestamp + Nonce
$timestamp = time();
$nonce = wp_create_nonce('mcf_timestamp_' . $timestamp);

// Honeypot Fields (3x, versteckt)
<input type="text" name="website" ... />
<input type="text" name="url" ... />
<input type="email" name="business_email" ... />
```

### 4.3 Plugin Registration

**Datei:** `src/Core/Plugin.php`

**Im Constructor:**
- FormRenderer initialisieren (Shortcode + Form HTML)
- FormHandler initialisieren (REST API für Submission)
- Beide `register()` aufrufen

---

## Phase 5: SMTP Konfiguration

### Ziel
Vollständige SMTP Implementation mit Password-Verschlüsselung und Test-Email.

### 5.1 Settings Model Extension

**Datei:** `src/Models/Settings.php`

**Neue Properties:**
```php
public array $smtp_config = [
    'enabled' => false,
    'host' => '',
    'port' => 587,
    'username' => '',
    'password' => '',  // Encrypted
    'encryption' => 'tls',
    'from_name' => '',
    'from_email' => '',
];
```

**Neue Methoden:**
- `encrypt_password($password)` - AES-256-CBC mit WP Salts
- `decrypt_password($encrypted)`
- `set_smtp_password($password)` - Auto-Encrypt
- `get_smtp_password()` - Auto-Decrypt
- Validation für SMTP Fields

### 5.2 TypeScript Types

**Datei:** `admin-app/src/types.ts`

```typescript
export interface SMTPConfig {
    enabled: boolean;
    host: string;
    port: number;
    username: string;
    password: string;
    encryption: 'tls' | 'ssl' | 'none';
    from_name: string;
    from_email: string;
}
```

### 5.3 React SMTP Component

**Datei:** `admin-app/src/components/SMTPSettings.tsx` (NEU)

- ToggleControl für Enable
- TextControls für Host, Port, Username, Password, From Name, From Email
- RadioControl für Encryption (TLS/SSL/None)
- Button "Send Test Email" mit API Call

### 5.4 Email Sending Implementation

**Datei:** `src/Public/FormHandler.php`

**Methoden:**
- `send_email($data)` - Haupt-Logik
- `build_email_message($data)` - HTML Email Template
- `configure_smtp($phpmailer)` - PHPMailer Hook für SMTP

**Ablauf:**
1. Recipient aus user_id auflösen
2. Email Message bauen (HTML Table mit allen Feldern)
3. Falls SMTP enabled: `phpmailer_init` Hook mit `configure_smtp()`
4. `wp_mail()` aufrufen
5. Fehlerbehandlung

### 5.5 Test Email REST Endpoint

**Datei:** `src/Admin/RestAPI.php`

**Neue Route:** `POST /mcf/v1/test-email`

- Akzeptiert smtp_config Parameter
- Speichert temporär
- Sendet Test-Email an aktuellen Admin
- Stellt alte Config wieder her
- Gibt Success/Error zurück

### 5.6 App.tsx Integration

**Datei:** `admin-app/src/App.tsx`

SMTPSettings Component in Sidebar einbinden mit onChange Handler.

---

## Phase 6: Integration & Testing

### 6.1 REST API Password Handling

**Datei:** `src/Admin/RestAPI.php`

In `update_settings()`:
- Prüfe ob SMTP Password geändert wurde (Vergleich mit alter Version)
- Nur re-encrypten wenn wirklich neu
- Verhindert doppelte Verschlüsselung

### 6.2 Frontend JavaScript

**Datei:** `assets/public/js/form.js` (NEU)

AJAX Form Submission:
- Prevent Default
- FormData → JSON
- POST zu `/wp-json/mcf/v1/submit`
- Success: Zeige Notice, Reset Form
- Error: Zeige Error Notice
- Loading State auf Submit Button

### 6.3 Script Enqueue

**Datei:** `src/Public/Frontend.php`

Enqueue `form.js` mit Dependency-Check.

---

## Kritische Dateien (Pfade)

### Neu zu erstellen:
1. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/admin-app/src/components/FieldGroupManager.tsx`
2. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/admin-app/src/components/CSSEditor.tsx`
3. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/admin-app/src/components/SMTPSettings.tsx`
4. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/src/Public/FormRenderer.php` (NEU - Form HTML Rendering)
5. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/src/Public/FormHandler.php` (NEU - Form Submission)
6. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/assets/public/css/themes/modern.css`
7. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/assets/public/css/themes/minimal.css`
8. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/assets/public/js/form.js`

### Zu modifizieren:
1. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/admin-app/src/types.ts`
2. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/admin-app/src/App.tsx`
3. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/admin-app/src/admin.scss`
4. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/admin-app/src/components/ThemeSelector.tsx`
5. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/admin-app/src/components/AdvancedStyling.tsx`
6. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/src/Core/Migration.php`
7. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/src/Core/Activator.php`
8. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/src/Core/Plugin.php`
9. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/src/Models/Settings.php`
10. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/src/Models/FieldConfig.php`
11. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/src/Admin/RestAPI.php`
12. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/src/Public/Frontend.php`

### Zu löschen:
1. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/admin-app/src/components/FieldManager.tsx`

### Legacy (nicht editieren):
1. `/Users/mirkoschubert/Projects/cms/wordpress/plugins/minimal-contact-form/public/class-form.php` - Wird durch FormRenderer ersetzt

---

## Edge Cases & Lösungen

### Migration
- **Custom CSS von v0.10.0:** Wird zu `styling.custom_css` migriert
- **Manuelle DB-Edits:** Activator erstellt Defaults, Migration hat Fallbacks
- **Fehlende Keys:** Migration prüft mit `??` Operator

### Security
- **Bot füllt Honeypot:** Silently rejected (403)
- **Zu schnelle Submission:** Rejected (< 3s = Bot)
- **Alte Form (> 1h):** Rejected, User muss refreshen
- **Shared IP:** Rate Limit könnte legitime User betreffen (dokumentieren!)
- **Session fehlt:** Code prüft `session_status()`

### SMTP
- **Passwort bereits verschlüsselt:** REST API vergleicht mit alter Version
- **WP Salts geändert:** Decrypt failed → Admin Notice + Re-Enter
- **Connection Timeout:** Error Log + klare Admin Message
- **Fehlende PHPMailer:** WP hat's built-in, aber Check hinzufügen

### CSS Editor
- **Invalide CSS:** CodeMirror zeigt Syntax Error, bricht Preview nicht
- **Sehr langes CSS:** 500ms Debounce verhindert Performance-Issues
- **JS disabled:** Preview funktioniert nicht, aber Saving schon

### Theme System
- **CSS-Datei fehlt:** Inline Fallback Styles in Frontend.php
- **Invalides Hex:** Sanitize mit `sanitize_hex_color()`

---

## Implementierungs-Reihenfolge

### Woche 1
- Phase 1: Field Groups (TypeScript, Migration, React, Backend)
- Phase 2: Theme System (CSS-Dateien, Loading, Frontend)

### Woche 2
- Phase 3: CSS Editor (CodeMirror, Preview, Advanced Styling)
- Phase 4: Security (FormHandler, alle 4 Features)

### Woche 3
- Phase 5: SMTP (Settings Model, React Component, Email Sending, Test)
- Phase 6: Integration (REST API, Frontend JS, Testing)

---

## Testing Checklist

### Phase 1
- [ ] Migration: split name → mode: split
- [ ] Migration: single name → mode: single
- [ ] Migration: phone enabled → mode: email-phone
- [ ] FieldGroupManager rendert alle Groups
- [ ] Toggles funktionieren
- [ ] Live-Vorschau zeigt Felder korrekt

### Phase 2
- [ ] modern.css lädt beide Varianten
- [ ] minimal.css lädt beide Varianten
- [ ] data-theme-variant="light" funktioniert
- [ ] data-theme-variant="dark" funktioniert
- [ ] Primary Color Override generiert Inline CSS
- [ ] Custom CSS wird angehängt

### Phase 3
- [ ] CodeMirror initialisiert
- [ ] CSS Syntax Highlighting
- [ ] Autocomplete zeigt MCF Variablen
- [ ] Preview lädt in iframe
- [ ] Preview updated mit 500ms Debounce
- [ ] Toggle zeigt/versteckt Preview

### Phase 4
- [ ] Honeypot Fields rendern (versteckt)
- [ ] Honeypot Validation rejectet gefüllte Felder
- [ ] Time-Check rejectet < 3s
- [ ] Time-Check rejectet > 1h
- [ ] CSRF Token validiert
- [ ] Rate Limit blockiert 4. Submission
- [ ] Rate Limit erlaubt nach 10min

### Phase 5
- [ ] SMTP Config speichert
- [ ] Passwort wird verschlüsselt
- [ ] Passwort wird entschlüsselt
- [ ] Test Email sendet erfolgreich
- [ ] Form Submission sendet via SMTP
- [ ] Email enthält alle Felder
- [ ] From Address verwendet SMTP Config

### Phase 6
- [ ] Frontend Form submitted via AJAX
- [ ] Success Notice zeigt
- [ ] Error Notice zeigt
- [ ] Keine Console Errors (Admin)
- [ ] Keine Console Errors (Frontend)
- [ ] Keine PHP Errors in error_log

---

## Wichtige Hinweise

### FieldGroupManager = EINZIGE Live-Vorschau!
Der FieldGroupManager zeigt ALLES:
- Formular-Felder basierend auf field_groups (readonly inputs)
- Theme-Styling (via data-theme-variant Attribut)
- Custom CSS (injiziert via `<style>` Tag inline)

Es gibt KEINE separate Preview-Komponente! Alles läuft über FieldGroupManager.

### Nur neue Struktur!
ALLE Änderungen in `src/`, `assets/`, `admin-app/`. Legacy-Code in `public/`, `includes/` etc. ist DEPRECATED und wird NICHT editiert!

**Neue Klassen ersetzen Legacy:**
- `src/Public/FormRenderer.php` ersetzt `public/class-form.php` (Form HTML)
- `src/Public/FormHandler.php` für Submission (komplett neu)

### Security in PSR-4!
Honeypot, CSRF, Time-Check, Rate Limiting werden in der NEUEN `src/Public/FormHandler.php` implementiert, NICHT im Legacy-Code!
