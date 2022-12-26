<template>

    <div>
        <div class="py-2 px-2 bg-grey-lightest">
            <input type="text" class="w-full px-2 py-2 text-sm" v-model="searchQuery" placeholder="Search or start new chat" />
        </div>

        <div class="bg-grey-lighter flex-1 overflow-auto">
            <div  v-for="(user, index) in filteredUsersList"
                :key="index">
            <div v-if="user.id !==$page.props.auth.user.id" class="px-3 flex items-center bg-grey-light cursor-pointer">
                <div>
                    <img class="h-12 w-12 rounded-full" :src="user.avatar" />
                </div>
                <div class="ml-4 flex-1 border-b border-grey-lighter py-4">
                    <div class="flex items-bottom justify-between">
                        <p class="text-grey-darkest">
                            {{ user.name }}
                        </p>
                        <p class="text-xs text-grey-darkest">
                            12:45 pm
                        </p>
                    </div>
                    <!-- <p class="text-grey-dark mt-1 text-sm">
                        Get Andrés on this movie ASAP!
                    </p> -->
                </div>
            </div>
            </div>
          
        </div>
    </div>
</template>

<script>
export default {
    props: {
        users: Array,

    },
    data() {
        return {
            searchQuery: ''
        }
    },
    computed: {
        filteredUsersList() {
            return this.users.filter(row => row.name.toLowerCase().includes(this.searchQuery.toLowerCase()))
        }
    }
}
</script>

<style>

</style>