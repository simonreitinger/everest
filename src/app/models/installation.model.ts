import { PackageLockModel } from './package-lock.model';

export interface InstallationModel {
  hash: string;
  url: string;
  cleanUrl: string;
  managerUrl: string;
  lastUpdate?: string;
  added?: string;
  favicon: string;
  title: string;
  themeColor: string;
  contao: {
    version: string;
    api: string;
    supported: boolean;
  };
  composer: {
    json: {
      found: boolean;
      valid: boolean;
      error?: string;
    };
    lock: {
      found: boolean;
      fresh: boolean;
    };
    vendor: {
      found: boolean;
    };
  };
  manager: {
    server: string;
    disable_cloud: boolean;
    last_update: string;
    latest_version: string;
  };
  phpCli: {
    version: string;
    version_id: number;
    problem?: string;
  };
  phpWeb: {
    version: string;
    version_id: number;
    platform: string;
    problem?: string;
  };
  config: {
    server: string;
    php_cli: string;
    detected: boolean;
    cloud: {
      enabled: boolean;
      issues: string[]
    }
  };
  selfUpdate: any[];
  packages: {
    name: string;
    version: string;
    version_normalized: string;
    require: {};
    conflict: {};
    'require-dev': {};
    type: string;
    extra: {};
    autoload: {};
    'autoload-dev': {};
    scripts: {};
    license: string[];
    authors: {}[];
    description: string;
    repositories: RepositoryModel[];
    'minimum-stability': string;
  };
  composerLock: PackageLockModel[];
}

export interface RepositoryModel {
  type: string;
  url: string;
}
