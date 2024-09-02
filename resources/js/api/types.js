import request from '@/utils/request';
import Resource from '@/api/resource';

class TypesResource extends Resource {
  constructor() {
    super('types');
  }

  getTypes(){
    return request({
      url: '/' + this.uri + '/get/Types',
      method: 'get',
    });}

  updateStatus(data){
    return request({
      url: '/' + this.uri + '/update/Status',
      method: 'post',
      data: data, 
    });
  }

}

export { TypesResource as default };