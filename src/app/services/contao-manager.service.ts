import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';

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
    const host = window.location.origin;
    return url
      + '/#/oauth?scope=admin&client_id=everest&return_url=' + host + '/websites/add%3Forigin=' + this.stripManagerUrl(url);
  }

  stripManagerUrl(url: string) {
    return url.replace('/' + CONTAO_MANAGER, '');
  }

  // generate manager url depending on last char of url
  // for slashes, it's not appended
  getManagerUrl(url: string) {
    if (url.includes(CONTAO_MANAGER)) {
      return url;
    }

    try {
      return (new URL(url)).origin + '/' + CONTAO_MANAGER;
    } catch (err) {
    }

    if (url.slice(-1) === '/') {
      return url + CONTAO_MANAGER;
    }

    return url + '/' + CONTAO_MANAGER;
  }

  saveUrlAndToken(url: string, token: string) {
    return this.http.post(environment.everestApi + '/website/add', { url, token });
  }
}
