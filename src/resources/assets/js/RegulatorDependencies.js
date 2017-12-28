Vue.component('permissions', require('./components/Regulator/ListPermissions.vue'));
Vue.component('permission-list-item', require('./components/Regulator/PermissionListItem.vue'));

window.Vuex = require('vuex');

const store = new Vuex.Store({
  	state: {
        permissions: [],
        role_permissions: []
  	},
	mutations: {
        setCurrentRole: (state, role) => {
			state.role = role;
        },
		setPermissions: (state, permissions) => {
			state.permissions = permissions;
		},
		setExistingPermissions: (state, permissions) => {

			state.role_permissions = permissions;

		},
	},
    actions: {

		addPermission: function ({ commit, state }, {permission}) {
            state.role_permissions.push(permission);

            state.permissions = state.permissions.filter(function (item) {
                return permission.id != item.id;
            });

        },

        removePermission: function ({ commit, state }, {permission}) {

            state.permissions.push(permission);

            state.role_permissions = state.role_permissions.filter(function (item) {
                return permission.id != item.id;
            });

        }
    }
});

const app = new Vue({
	el: '#regulator-app',
	store
});
