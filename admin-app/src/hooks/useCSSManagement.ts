import { useState, useEffect } from '@wordpress/element'
import { generatePrimaryColorCSS } from '../utils/colorUtils'

interface UseCSSManagementProps {
  theme: string
  customCSS: string
  primaryColor?: string
}

/**
 * Custom hook to manage CSS loading and injection for form preview
 * Handles 3 layers: Base+Theme CSS (API), Custom CSS (user), Primary Color CSS (generated)
 */
export function useCSSManagement({ theme, customCSS, primaryColor }: UseCSSManagementProps) {
  const [baseThemeCSS, setBaseThemeCSS] = useState<string>('')

  // Load Base + Theme CSS via API (only when theme changes)
  useEffect(() => {
    const loadBaseThemeCSS = async () => {
      try {
        const response = await fetch(`${window.mcfAdmin.apiUrl}/preview-css?theme=${theme}`, {
          headers: {
            'X-WP-Nonce': window.mcfAdmin.nonce
          }
        })

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }

        const data = await response.json()

        if (data.success) {
          setBaseThemeCSS(data.css)
        } else {
          console.error('API returned unsuccessful response:', data)
        }
      } catch (error) {
        console.error('Failed to load base/theme CSS:', error)
        setBaseThemeCSS('')
      }
    }

    loadBaseThemeCSS()
  }, [theme])

  // Consolidate all CSS layers and inject into document head
  useEffect(() => {
    // Layer 1: Base + Theme CSS (from API)
    let consolidatedCSS = baseThemeCSS

    // Layer 2: Custom CSS (if theme is 'custom')
    if (theme === 'custom' && customCSS) {
      consolidatedCSS += '\n\n' + customCSS
    }

    // Layer 3: Primary Color CSS (client-side generated)
    if (primaryColor) {
      const primaryColorCSS = generatePrimaryColorCSS(primaryColor)
      consolidatedCSS += '\n\n' + primaryColorCSS
    }

    // Inject into document head
    const styleElement = document.getElementById('mcf-preview-css')

    if (styleElement) {
      styleElement.textContent = consolidatedCSS
    } else if (consolidatedCSS) {
      const style = document.createElement('style')
      style.id = 'mcf-preview-css'
      style.textContent = consolidatedCSS
      document.head.appendChild(style)
    }

    // Cleanup on unmount
    return () => {
      const element = document.getElementById('mcf-preview-css')
      if (element) {
        element.remove()
      }
    }
  }, [baseThemeCSS, theme, customCSS, primaryColor])
}
