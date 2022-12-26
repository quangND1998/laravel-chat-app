<template>
    <div>

        <Head title="Dashboard" />

        <BreezeAuthenticatedLayout>
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Chat room: {{ room }}
                </h2>
                <div>
                    Shareable link: <code class="bg-yellow-300 rounded-lg px-2 py-0.5 select-all">{{ link }}</code>
                </div>
                <div class="my-3 text-white font-bold text-lg text-center">
                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-green-500 text-white rounded">{{ users.length }} User online</span>
                </div>
            </template>

            <div class="mb-3 xl:w-96">
                    <div class="flex justify-center">
                        <div class="mb-3 xl:w-96">
                            <select @change="changeState($event.target.value)"
                                class="form-select appearance-noneblock w-20 px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none">
                                <option ></option>
                                <option v-for="(emoji,index) in emojis" :key="index" :value="emoji">{{ emoji }}</option>
                                
                            </select>
                        </div>
                    </div>
                    <ul
                    class="w-48 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                    <div v-for="(user,index) in users" :key="index" >
                        <li class="py-2 px-4 w-full rounded-t-lg border-b border-gray-200 dark:border-gray-600">
                            <span >{{ user.state }}</span>
                            -
                            <strong >{{ user.name }}</strong>
                        </li>
                    </div>
                </ul>

                </div>

                
            <div class="py-12">
                <div class="max-w-7xl flex items-start mx-auto sm:px-6 lg:px-8 space-x-2">
                    <div class="w-1/4 bg-white shadow-sm sm:rounded-lg p-3 text-sm">
                        <div v-for="user in users" :key="user.id" :class="{
                            'font-bold': $page.props.auth.user.id === user.id,
                        }">
                            {{ user.name }}
                        </div>
                    </div>
               
                    <div class="flex flex-col space-y-3 flex-1 h-96 overflow-y-auto">
                        <div class="bg-white shadow-sm sm:rounded-lg p-3 w-full" v-for="(line, i) in lines" :key="i">
                            <div :class="{
                                'text-red-500': line.type === 'error',
                                'italic text-gray-600': line.type === 'system',
                            }">
                                <div v-if="line.type === 'message'" class="font-bold">
                                    {{ line.user.name }}
                                </div>
                                <div v-html="line.message">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                  
                    
                </div>
               
                <span class="inline-block" v-if="UserTyping">
                        <img src="/images/typing.gif" class="w-16 " />
                        {{ UserTyping.name }} is Typing ...
                    </span>
               
            </div>
          
            <div class="bottom-auto p-6 bg-white w-full">
                <input v-model="message" 
        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block max-w-7xl w-full mx-auto" type="text" 
                    placeholder="Type your message here and press ENTER..." @keydown="sendTypingEvent" @keyup.enter="sendMessage"      />
                    <i class="fa-solid fa-face-smile mr-1" @click="sendIcon('fa-solid fa-face-smile mr-1')"></i>
                    <i class="fa-solid fa-user mr-1"  @click="sendIcon('fa-solid fa-user mr-1')"></i>
            </div>

        </BreezeAuthenticatedLayout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import BreezeInput from '@/Components/Input.vue'
import { Head } from '@inertiajs/inertia-vue3';

export default {
    components: {
        BreezeAuthenticatedLayout,
        BreezeInput,
        Head,
    },

    props: [
        'room',
        'link',
        'user'
    
    ],

    data() {
        return {
            lines: [],
            users: [],
            message: '',
            emojis : [ '💪', '👀', '🥳', '😍', '🥰', '😎', '😂', '🤗','😆','😅','🤣'],
            UserTyping: false,
            typingTimer: false
        };
    },

    mounted() {
        Echo.join(`room.${this.room}`)
            .here(users => {
                this.users = users;
                this.systemMessage('You have joined the channel.');
            })
            .joining(user => {
                console.log(user)
                this.users.push(user);
                this.systemMessage(`${user.name} joined the channel.`);
            })
            .leaving(user => {
                this.users.splice(this.users.indexOf(user), 1);
                this.systemMessage(`${user.name} left the channel.`);
            })
            .listen('.room.message', ({ message, user }) => {
                this.userMessage(message, user);
            }).listenForWhisper('typing', user => {
                console.log('typing', user)
                this.UserTyping = user;
                if (this.typingTimer) {
                    clearTimeout(this.typingTimer)
                }
                this.typingTimer = setTimeout(() => {
                    this.UserTyping = false;
                }, 2000)
                // console.log(this.isSeen)
            })
            .listenForWhisper('changeState', (e) => {
                console.log(e)
                this.users.map(user => {
                    if (user.id == e.userId) {
                        user.state = e.state
                    }
                })

            });
        // window.Echo.channel("sovled-conversation").listen(
        //     "SovledConversationEvent",
        //     e => {
        //         console.log(e);


        //         // this.conversations.data[index].solved = e.solved
        //     }
        // );
    },

    methods: {
        systemMessage(message) {
            this.lines.push({ message, type: 'system' });
        },

        errorMessage(message) {
            this.lines.push({ message, type: 'error' });
        },

        userMessage(message, user) {
            this.lines.push({ message, user, type: 'message' });
        },

        sendMessage() {
            let message = this.message;
            this.message = '';
           

            axios.post(this.route('send.message'), { room: this.room, message }).then(() => {
                //
            });
        },

        sendIcon(icon){
            let message =  `<i class="${icon}"></i>`;
            this.message = '';
           

            axios.post(this.route('send.message'), { room: this.room, message }).then(() => {
                //
            });
        },
        sendTypingEvent(){
            // console.log(this.room)
            Echo.join(`room.${this.room}`)
                .whisper('typing', this.user);
        },
        changeState(state){
            Echo.join(`room.${this.room}`)
                .whisper('changeState', {
                    state,
                    userId: this.user.id
                });
    

        }
     
        
    }
}
</script>
