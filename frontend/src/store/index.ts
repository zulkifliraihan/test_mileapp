import { createStore } from 'vuex'
import auth from './modules/auth'
import tasks from './modules/tasks'

export default createStore({
  modules: {
    auth,
    tasks,
  },
})
