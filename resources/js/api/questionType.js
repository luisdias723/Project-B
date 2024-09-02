import request from '@/utils/request';
import Resource from '@/api/resource';

class QuestionTypeResource extends Resource {
    constructor() {
        super('questionType');
    }

    getQuestionType(data) {
        return request({
            url: '/' + this.uri + '/get/questionType',
            method: 'get',
            params: data,
        });
    }

}

export { QuestionTypeResource as default };
