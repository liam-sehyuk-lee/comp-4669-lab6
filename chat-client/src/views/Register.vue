<template>
  <div class="flex justify-center mt-20">
    <div class="card w-96 bg-gray-800 shadow-xl">
      <div class="card-body">
        <h2 class="card-title justify-center">Register</h2>

        <small v-if="registrationError" class="text-red-600 ml-5 mt-3">
          {{ registrationError }}
        </small>

        <div class="form-control w-full max-w-xs mt-5">
          <input
              type="text"
              v-model="name"
              placeholder="Your name"
              class="input input-bordered w-full max-w-xs"
          />
          <input
              type="email"
              v-model="email"
              placeholder="Your email"
              class="input input-bordered w-full max-w-xs mt-5"
          />
          <input
              type="password"
              v-model="password"
              placeholder="Your password"
              class="input input-bordered w-full max-w-xs mt-5"
          />

          <button @click="register" class="btn btn-primary mt-5">
            Register
          </button>

          <p class="mt-4 ml-5">
            Already have an account?
            <RouterLink to="/login">Login</RouterLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {ofetch} from "ofetch";
import {useUserStore} from "@/stores/UserStore";

export default {
  name: "Register",
  data() {
    return {
      registrationError: "",
      name: "",
      email: "",
      password: "",
    };
  },
  methods: {

    register() {
      this.axios.get("/sanctum/csrf-cookie").then(_ => {
        this.axios.post("/register", {
          name: this.name,
          email: this.email,
          password: this.password,
        }).then((_) => {
          this.axios.get("/api/user").then(response => {
            localStorage.setItem('authenticated', 'true')
            this.$router.push({path: "/"});
          }).catch((error) => {
            console.log("register error")
            console.log(error.response.status);
          });
        }).catch((error) => {
          if (
              error.response.status === 401 ||
              error.response.status === 422
          ) {
            this.registrationError = error.response.data.message;
          }
        });
      }).catch(error => {
        // Not really unknown, but there's not a lot of useful info we can provide to an end-user.
        // If this happens, the API could be down, or potentially we could have a CSRF token mismatch.
        this.loginError = "An unknown error has occurred. Please try again later."
      });
    },
  },
};
</script>

<style scoped></style>
