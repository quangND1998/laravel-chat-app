<template>

    <div>

        <div class="py-2 px-2 bg-grey-lightest">
            <input type="text" class="w-full px-2 py-2 text-sm" v-model="searchQuery" placeholder="Search or start new chat" />
        </div>

        <div class="bg-grey-lighter flex-1 overflow-auto" >
            <div  v-for="(user, index) in filteredUsersList"   
                :key="index" >
            <div v-if="user.id !==$page.props.auth.user.id" class="bg-white px-3 flex items-center hover:bg-gray-200 cursor-pointer"  :class="[user.new_messages ? 'blink-anim' : '', userSelected !==null  && userSelected.id == user.id ? 'bg-gray-200':'' ]"   @click="$emit('selectReceiver', user)">
                <div>
                    <img class="h-12 w-12 rounded-full" :src="user.avatar" />
                </div>
                <div class="ml-4 flex-1 border-b border-grey-lighter py-4">
                    <div class="flex items-bottom justify-between">
                        <p class="text-grey-darkest">
                            {{ user.name }}
                        </p>
                        <p class="text-xs text-green-600">
                            <i class="fa-solid fa-circle"></i> 
                        </p>
                    </div>
                    <!-- <p class="text-grey-dark mt-1 text-sm">
                        Get Andrés on this movie ASAP!
                    </p> -->
                </div>
                <div class="user_info">
              <span
                class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-red-600 text-white rounded-full"
                v-if="user.new_messages"
              >
                {{ user.new_messages }}
              </span>
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
        userSelected:Object
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