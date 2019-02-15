export interface MonitoringModel {
  hash: string;
  createdAt: string;
  status: number;
  statusText: string;
  failed: boolean;
  requestTimeInMs: number;
}
