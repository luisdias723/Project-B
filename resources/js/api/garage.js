import request from '@/utils/request';
import Resource from '@/api/resource';

class GarageResource extends Resource {
  constructor() {
    super('garage');
  }

  getTruckFleets(data){
    return request({
      url: '/' + this.uri + '/get/truckFleets',
      method: 'get',
      params: data,
    });
  }

  getTruckStatus(data){
    return request({
      url: '/' + this.uri + '/get/truckStatus',
      method: 'get',
      params: data,
    });
  }
    
  getTruckBreakdows(data){
    return request({
      url: '/' + this.uri + '/get/truckBreakdowns',
      method: 'get',
      params: data,
    });
    
  }

  updateStatus(data){
    return request({
      url: '/' + this.uri + '/update/Status',
      method: 'post',
      data: data, 
    });
  }

}

export {GarageResource as default };