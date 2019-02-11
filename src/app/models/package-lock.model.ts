export interface PackageLockModel {
  name: string;
  vendor: string;
  repository: string;
  description: string;
  version: string;
  versionNormalized: string;
  rootVersion: string;
  inRoot: boolean;
  isPrivate: boolean;
  checked: boolean;
}
