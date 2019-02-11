export interface TaskOutputModel {
  title: string;
  summary: string;
  detail: string;
  console: string;
  cancellable: boolean;
  autoclose: boolean;
  audit: boolean;
  status: string;
}
