import { registerBlockType } from '@wordpress/blocks'
import type { BlockConfiguration } from '@wordpress/blocks'
import './editor.scss'
import edit from './edit'
import save from './save'
import type { BlockAttributes } from './types'

// Register block type
registerBlockType<BlockAttributes>('mcf/contact-form', {
  title: 'Contact Form',
  category: 'widgets',
  icon: 'email',
  description: 'A minimal, secure contact form with customizable styling',
  keywords: ['contact', 'form', 'email', 'minimal'],
  supports: {
    html: false,
    multiple: true,
    align: ['wide', 'full']
  },
  attributes: {
    theme: {
      type: 'string',
      default: ''
    },
    variant: {
      type: 'string',
      default: ''
    },
    primaryColor: {
      type: 'string',
      default: ''
    },
    customCSS: {
      type: 'string',
      default: ''
    },
    privacyMode: {
      type: 'string',
      default: ''
    }
  },
  edit,
  save
} as BlockConfiguration<BlockAttributes>)
