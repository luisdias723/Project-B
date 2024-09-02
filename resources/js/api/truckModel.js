import request from '@/utils/request';
import Resource from '@/api/resource';

class TruckModelResource extends Resource {
  constructor() {
    super('truckModels');
  }

  getTruckModels(data){
    return request({
      url: '/' + this.uri + '/get/truckModels',
      method: 'get',
      params: data,
    });}

}

export {TruckModelResource as default };