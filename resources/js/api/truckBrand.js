import request from '@/utils/request';
import Resource from '@/api/resource';

class TruckBrandResource extends Resource {
  constructor() {
    super('truckBrands');
  }

  getTruckBrands(data){
    return request({
      url: '/' + this.uri + '/get/truckBrands',
      method: 'get',
      params: data,
    });}

}

export {TruckBrandResource as default };