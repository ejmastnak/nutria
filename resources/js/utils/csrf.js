// Adds an axios interceptor to refresh expired CSRF tokens
import get from 'lodash/get'

axios.interceptors.response.use(response => response, async err => {
  const status = get(err, 'response.status')
  if (status === 419) {
    // Regenerate CSRF token
    await axios.get('/csrf-token')
    // Retry original request with new token
    return axios(err.response.config)
  }
  // Reject promise if status isn't 419
  return Promise.reject(err)
})
