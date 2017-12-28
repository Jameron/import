<template>
    <div>
        <select v-model="selected" class="form-control">
            <option :value="null">Select an option</option>
            <option v-for="(permission, index) in permissions"
                    v-bind:value='permission.id'
            >
            {{ permission.name }}
            </option>
        </select>
        <a href="#" @click="addPermission($event)">Add Permission</a>
        <hr>
        <ul>
            <li v-for="(permission, index) in role_permissions">{{ permission.name }} <a href="#" @click="removePermission(permission.id)">remove</a>
            <input type="hidden" name="permissions[]" v-bind:value="permission.id">
            </li>
        </ul>
    </div>
</template>
<script>
	export default {
        data() {
            return {
                selected: null,
            }
        },

        props: {
            php_permissions: Array,
            php_existing_permissions: Array
        },

		created() {
			this.setPermissions();
			this.setExistingPermissions();
        },

        methods: {
            removePermission: function(permission_id) {

                event.preventDefault();
                var dat = this;
                const permission = this.role_permissions.find(function(permission) {
                    return permission.id == permission_id
                })
                if(permission) {
                    this.$store.dispatch('removePermission', {
                        permission: permission,
                    });
                }

            },
            addPermission: function(event) {

                event.preventDefault();
                var dat = this;
                const permission = this.permissions.find(function(permission) {
                    return permission.id == dat.selected
                })

                if(permission) {
                    this.$store.dispatch('addPermission', {
                        permission: permission,
                    });
                }

            },
            setPermissions: function () {
                this.$store.commit('setPermissions', this.php_permissions);
            },
            setExistingPermissions: function () {
                this.$store.commit('setExistingPermissions', this.php_existing_permissions);
            },
        },
		computed: {
            role_permissions() {
				return this.$store.state.role_permissions;
            },
			permissions() {
				return this.$store.state.permissions;
			}
		},
    }
</script>
