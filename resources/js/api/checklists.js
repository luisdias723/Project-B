import request from '@/utils/request';
import Resource from '@/api/resource';

class ChecklistResource extends Resource {
  constructor() {
    super('checklists');
  }

  getChecklists(data){
    return request({
      url: '/' + this.uri + '/get/checklists',
      method: 'get',
      params: data,
    });}

}

export { ChecklistResource as default };
