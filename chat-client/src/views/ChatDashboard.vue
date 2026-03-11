<template>

  <div class="grid grid-cols-12">

    <div class="col-span-4 ml-14 pr-3 br-3 pl-3">
      <div>
        <label for="my-modal-5" class="btn bg-info mt-12 w-full text-black hover:text-gray-600 hover:bg-info">New Chat
          Room</label>
        <input type="checkbox" id="my-modal-5" class="modal-toggle"/>
        <div class="modal">
          <div class="modal-box w-11/12 max-w-2xl bg-gray-800">
            <label for="my-modal-5" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
            <h3 class="font-bold text-lg">Create a new chat room</h3>

            <div class="form-control w-full">
              <input
                  type="text"
                  v-model="name"
                  placeholder="Name"
                  class="input input-bordered w-full w-full mt-5"
              />
            </div>

            <div class="modal-action">
              <label for="my-modal-5" class="btn btn-secondary">Cancel</label>
              <button class="btn" @click="createRoom">Create</button>
            </div>
          </div>
        </div>
      </div>

      <div class="max-h-96 overflow-y-scroll mt-2">
        <label for="my-drawer" class="drawer-overlay"></label>
        <span v-if="rooms && rooms.length === 0" class="ml-4">
            No chat rooms yet.
          </span>
        <ul v-for="room in rooms" :key="currentRoomId" class="menu p-1 text-base-content">
          <li class="bg-gray-800 rounded-btn" @click="switchRoom(room.id)">
            <a class="hover:no-underline"
               :class="{ 'active': String(room.id) === String(currentRoomId) }">
              {{room.name }}
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!--  chat area  -->
    <div v-if="String(currentRoomId) === String(-1)" class="col-span-8 mt-11 ml-11 mr-3 pb-3 pr-3 br-3 pl-3 relative bottom-0 mr-10">
      <h2 class="card-title mt-4">No room selected yet! Create or select one on the left hand side.</h2>
    </div>
    <div v-else id="chat-box"
         class="col-span-8 mt-11 ml-11 mr-3 bg-gray-700 pb-3 pr-3 br-3 pl-3 rounded-box relative bottom-0 mr-10">
      <div class="max-h-96 overflow-y-scroll pb-6 pt-5" id="messages">
        <div v-for="message in messages" :key="roomRefreshKey">
          <div v-if="message.from_user_id === user.id">
            <!--     Indicating the message is from the currently authenticated user       -->
            <div class="chat chat-end">
              <div class="chat-bubble chat-bubble-secondary">{{ message.body }}</div>
              <br>
              <div><small v-if="message.user">{{ message.user.name }}</small></div>
            </div>
          </div>
          <div v-else>
            <!--      The message is from someone else in the room      -->
            <div class="chat chat-start">
              <div class="chat-bubble chat-bubble-info">{{ message.body }}</div>
              <br>
              <div><small>{{ message.user.name }}</small></div>
            </div>
          </div>
        </div>

      </div>

      <div class="form-control mt-5 absolute bottom-0 mb-3 w-full pr-6">
        <div class="input-group">

          <input v-model="message" type="text" placeholder="Your message..." class="input input-bordered w-full"/>

          <button @click="sendMessage" class="btn btn-square btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import {useUserStore} from "@/stores/UserStore"
import _ from 'lodash'
import {Centrifuge} from 'centrifuge';

export default {
  components: {},
  data() {
    return {
      roomRefreshKey: 0,
      centrifuge: null,
      centrifugeToken: null,
      user: null,
      rooms: null,
      message: null,
      error: null,
      currentRoomId: -1,
      messages: [],

      // room creation
      name: null,
      description: null,
    }
  },
  created() {
    const store = useUserStore()
    if (_.isEmpty(store.user)) {
      this.axios.get("/api/user").then((response) => {
        store.user = response.data
        this.user = store.user
      }).catch(_ => {
        localStorage.setItem('authenticated', 'false')
        // Likely a 401 Unauthorized. Whatever the error case: redirect back to the login page.
        this.$router.push('/login')
      })
    } else {
      this.user = store.user
    }
    const token = localStorage.getItem('ws_token')
    if (_.isEmpty(token) || token === 'undefined') {
      this.getWsToken()
    }
    this.setupCentrifugo()
    this.getRooms()
    const roomId = localStorage.getItem('room');
    if (roomId) {
      this.currentRoomId = roomId
    }
    console.log("Switching to room: " + this.currentRoomId)
    this.setInitialRoom(this.currentRoomId)
  },
  methods: {

    setupCentrifugo() {
      const wsToken = localStorage.getItem('ws_token')

      this.centrifuge = new Centrifuge("ws://localhost:8500/connection/websocket", {
        token: wsToken,
        getToken: this._getToken,
      });

      this.centrifuge.on('connecting', function (ctx) {
        console.log(`connecting: ${ctx.code}, ${ctx.reason}`)
      }).on('connected', function (ctx) {
        console.log(`connected over ${ctx.transport}`)
      }).on('disconnected', function (ctx) {
        console.log(`disconnected: ${ctx.code}, ${ctx.reason}`)
        console.log(ctx)
      }).on('error', ctx => {
        if (ctx.error.code === 109) {
          this.getWsToken() // old token expired, get a new one
        } else if (ctx.error.code === 11) {
          this.centrifuge.connect() // try to reconnect
        } else {
          this.getWsToken() // hopefully requesting a new token resolves the issue
          console.log(ctx.error)
        }
      }).connect()
    },

    setupRoomSubscription(newRoomId) {
      let subscription = null;
      try {
        subscription = this.centrifuge.newSubscription(newRoomId.toString());
      } catch (e) {
        // Likely an error such as an already existing subscription.
        console.log(e)
        return
      }
      subscription.on('publication', ctx => {
        this.messages.unshift(ctx.data.message)
      }).on('subscribing', ctx => {
        console.log("subscribing...")
        console.log(ctx)
      }).on('subscribed', ctx => {
        console.log("subscribed")
        console.log(ctx)
      }).on('unsubscribed', ctx => {
        console.log(`unsubscribed: ${ctx.code}, ${ctx.reason}`)
      }).on('leave', ctx => {
        console.log("leaving!")
      }).on('error', ctx => {
        if (ctx.error.code === 109) {
          // old token expired, get a new one
          this.getWsToken()
        } else {
          console.log(ctx.error)
        }
      }).subscribe();
    },

    /**
     * @param roomId
     */
    setInitialRoom(roomId) {
      this.setupRoom(roomId)
    },

    /**
     * @param newRoomId
     */
    switchRoom(newRoomId) {
      if (this.currentRoomId !== newRoomId) {
        this.setupRoom(newRoomId)
      }
    },

    /**
     * @param roomId
     */
    setupRoom(roomId) {
      if (String(roomId) !== String(-1)) {
        localStorage.setItem('room', roomId)
      }
      this.currentRoomId = roomId
      this.setupRoomSubscription(roomId)
      this.getMessages()
    },

    /**
     * Get messages belonging to the current room
     */
    getMessages() {
      if (String(this.currentRoomId) !== String(-1)) {
        this.axios.get(`api/messages/${this.currentRoomId}`).then(response => {
          this.messages = response.data.messages.reverse()
        }).catch(error => {
          console.log(error)
        })
      }
    },

    createRoom() {
      this.axios.post("/api/rooms", {
        'name': this.name,
      }).then(room => {
        const roomId = room.id
        document.querySelectorAll('.modal-toggle')[0].click();
        this.getRooms();
        this.name = '';
      }).catch((e) => {
          console.log(e)
      });
    },

    getRooms() {
      this.axios.get('api/rooms').then(response => {
        this.rooms = response.data.rooms
        console.log(this.rooms)
      }).catch(error => {
        console.log(error)
      })
    },

    /**
     * Requests a message to be sent to the current room
     */
    sendMessage() {
      this.axios.post(`/api/messages/${this.currentRoomId}`, {
          body: this.message,
          room_id: this.currentRoomId,
        }).then(() => {
          this.message = "" // clear the text box
        }).catch(() => {
          this.error = "couldn't send message"
        });
    },

    /**
     * @returns {Promise<unknown>}
     * @private
     */
    _getToken() {
      return new Promise((resolve, reject) => {
        this.axios.get('api/ws_token').then(response => {
          const token = response.data.ws_token
          resolve(token)
          localStorage.setItem('ws_token', this.token)
        }).catch(error => {
          console.log("error getting a new WS token")
          reject(error)
          localStorage.removeItem('ws_token')
        })
      })
    },

    getWsToken() {
      this._getToken().then(token => {
        this.token = token
        localStorage.setItem('ws_token', token)
      }).catch(() => {
        console.log("error getting a new WS token")
        localStorage.removeItem('ws_token')
      })
    },

  }
}
</script>

<style scoped>
#messages {
  display: flex;
  flex-direction: column-reverse;
}

#chat-box {
  min-height: 100%;
}
</style>