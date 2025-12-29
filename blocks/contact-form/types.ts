// Re-export types from admin app
export type {
  MCFOptions,
  MCFSettings,
  MCFFieldConfig,
  MCFPrivacyTexts,
  MCFStyling,
} from '../../admin-app/src/types';

export interface BlockAttributes {
  theme: string;
  variant: string;
  primaryColor: string;
  customCSS: string;
  privacyMode: string;
}
