export interface MonitoringModel {
  websiteHash: string;
  createdAt: string;
  status: number;
  statusText: string;
  failed: boolean;
  requestTimeInMs: number;
}
