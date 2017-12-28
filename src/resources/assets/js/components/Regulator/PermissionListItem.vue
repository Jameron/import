<template>
    <div class="row">
        <div class="col-md-4">
            {{ permission.name }}?
        </div>
    </div>
</template>
<script>
	export default {
        props: {
            permission: Object,
        },
		created() {
        },
        methods: {
        },
		computed: {
		},
    }
</script>
