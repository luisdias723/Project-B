import request from '@/utils/request';
import Resource from '@/api/resource';

class UserResource extends Resource {
  constructor() {
    super('users');
  }

  register(data) {
    return request({
      url: '/' + this.uri + '/register',
      method: 'post',
      data: data,
    });
  }

  getResetLink(data) {
    return request({
      url: '/' + this.uri + '/getReset',
      method: 'post',
      data: data,
    });
  }

  updateActive(data) {
    return request({
      url: '/' + this.uri + '/update/active',
      method: 'post',
      data: data,
    });
  }

  updatePassword(data) {
    return request({
      url: '/' + this.uri + '/reset/password',
      method: 'post',
      data: data,
    });
  }

  changeAvatar(data) {
    return request({
      url: 'users/changeAvatar',
      method: 'post',
      data: data,
    });
  }

  getUsers() {
    return request({
      url: '/' + this.uri + '/get/Users',
      method: 'get',
    });
  }

}

export { UserResource as default };
