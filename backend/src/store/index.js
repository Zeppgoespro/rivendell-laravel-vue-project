import {createStore} from "vuex";
import state from './state';
import * as mutations from './mutations';
import * as actions from './actions';

const store = createStore({
    state,
    getters: {},
    actions,
    mutations,
});

export default store;
