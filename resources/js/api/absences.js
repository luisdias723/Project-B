import request from '@/utils/request';
import Resource from '@/api/resource';

class AbsencesResource extends Resource {
  constructor() {
    super('absences');
  }

  getEventsToCalendar(data){
    return request({
      url: '/' + this.uri + '/get/EventsToCalendar',
      method: 'get',
      params: data,
    });
  }

  updateFerias(data){
    return request({
      url: '/' + this.uri + '/update/Ferias',
      method: 'post',
      params: data,
    });
  }

}

export { AbsencesResource as default };
