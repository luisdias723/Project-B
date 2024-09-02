import request from '@/utils/request';
import Resource from '@/api/resource';

class VacationResource extends Resource {
    constructor(){
        super('vacations');
    }

    getVacations(query){
        return request({
            url: '/' + this.uri + '/get/Vacations',
            method: 'get',
            params: query,
        });
    }
}

export { VacationResource as default };