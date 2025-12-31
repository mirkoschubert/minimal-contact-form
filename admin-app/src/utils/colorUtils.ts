/**
 * Color utility functions for CSS generation
 * Provides WCAG 2.0 compliant contrast calculations
 */

/**
 * Adjusts the brightness of a hex color
 * @param hex - Hex color string (with or without #)
 * @param steps - Number of steps to adjust (-255 to 255)
 * @returns Adjusted hex color
 */
export function adjustBrightness(hex: string, steps: number): string {
  const cleanHex = hex.replace('#', '')
  let r = parseInt(cleanHex.substring(0, 2), 16)
  let g = parseInt(cleanHex.substring(2, 4), 16)
  let b = parseInt(cleanHex.substring(4, 6), 16)

  r = Math.max(0, Math.min(255, r + steps))
  g = Math.max(0, Math.min(255, g + steps))
  b = Math.max(0, Math.min(255, b + steps))

  return '#' + [r, g, b].map((x) => x.toString(16).padStart(2, '0')).join('')
}

/**
 * Calculates contrast color (black or white) based on WCAG 2.0 relative luminance
 * @param hex - Hex color string (with or without #)
 * @returns '#000000' or '#ffffff'
 */
export function getContrastColor(hex: string): string {
  const cleanHex = hex.replace('#', '')
  let r = parseInt(cleanHex.substring(0, 2), 16) / 255
  let g = parseInt(cleanHex.substring(2, 4), 16) / 255
  let b = parseInt(cleanHex.substring(4, 6), 16) / 255

  // Calculate relative luminance (WCAG 2.0)
  r = r <= 0.03928 ? r / 12.92 : Math.pow((r + 0.055) / 1.055, 2.4)
  g = g <= 0.03928 ? g / 12.92 : Math.pow((g + 0.055) / 1.055, 2.4)
  b = b <= 0.03928 ? b / 12.92 : Math.pow((b + 0.055) / 1.055, 2.4)

  const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b

  return luminance > 0.5 ? '#000000' : '#ffffff'
}

/**
 * Generates primary color CSS variables for form theming
 * @param color - Primary hex color
 * @returns CSS string with custom properties
 */
export function generatePrimaryColorCSS(color: string): string {
  if (!color) return ''

  const hoverColor = adjustBrightness(color, -20)
  const textColor = getContrastColor(color)
  const textHoverColor = getContrastColor(hoverColor)

  return `
#mcf-preview[data-theme-variant="light"],
#mcf-preview[data-theme-variant="dark"] {
  --mcf-button-background-color: ${color} !important;
  --mcf-button-background-hover-color: ${hoverColor} !important;
  --mcf-button-color: ${textColor} !important;
  --mcf-button-hover-color: ${textHoverColor} !important;
  --mcf-checkbox-color: ${color} !important;
}`
}
