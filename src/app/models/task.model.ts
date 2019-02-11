interface TaskModel {
  name: string; // e.g. 'composer/update'
  config: {
    require: string[];
    remove: string[];
    update: string[];
    dry_run: boolean;
  };
  website: string; // website.cleanUrl
  output?: string;
}
