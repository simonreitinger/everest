import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { WebsiteModel } from '../models/website.model';

export const CONTAO_MANAGER = 'contao-manager.phar.php';
export const defaultTask = {
  name: '',
  config: {
    'require': [],
    'remove': [],
    'update': [],
    'dry_run': false
  }
};

// this service is responsible for registering new installations
@Injectable({
  providedIn: 'root'
})
export class ContaoManagerService {

  constructor(private http: HttpClient) {
  }

  getRegisterUrl(url: string) {
    if (url.includes('localhost')) {
      return url
        + '/#/oauth?scope=admin&client_id=everest&return_url=http://localhost:4200/website/add%3Forigin=' + url;
    }
    return this.getManagerUrl(url)
      + '/#/oauth?scope=admin&client_id=everest&return_url=http://localhost:4200/website/add%3Forigin=' + url;
  }

  getManagerUrl(url: string) {
    if (url.includes('localhost')) {
      return url.replace('/' + CONTAO_MANAGER, '');
    }

    return url + '/' + CONTAO_MANAGER;
  }

  saveUrlAndToken(url: string, token: string) {
    return this.http.post(environment.everestApi + '/website/add', { url, token });
  }
}
