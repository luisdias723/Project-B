import request from '@/utils/request';
import Resource from '@/api/resource';

class TransportTypeResource extends Resource {
  constructor() {
    super('transportTypes');
  }

  getTransportTypes(data){
    return request({
      url: '/' + this.uri + '/get/transportTypes',
      method: 'get',
      params: data,
    });}

}

export {TransportTypeResource as default };