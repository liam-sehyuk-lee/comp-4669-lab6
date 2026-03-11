<template>
  <div class="flex justify-center mt-20">
    <div class="card w-96 bg-gray-800 shadow-xl">
      <div class="card-body">
        <h2 class="card-title justify-center">Login</h2>
        <Form @submit="login" class="form-control w-full max-w-xs">
          <small v-if="loginError" class="text-red-600 ml-5 mt-3">
            {{ loginError }}
          </small>

          <Field
            name="email"
            type="email"
            v-model="email"
            :rules="validateEmail"
            placeholder="Your email"
            class="input input-bordered w-full max-w-xs mt-5"
          />
          <small class="text-red-600 ml-5 mt-1">
            <ErrorMessage name="email" />
          </small>
          <Field
            :rules="validatePassword"
            name="password"
            type="password"
            v-model="password"
            placeholder="Your password"
            class="input input-bordered w-full max-w-xs mt-5"
          />
          <small class="text-red-600 ml-5 mt-1">
            <ErrorMessage name="password" />
          </small>
          <button class="btn btn-primary mt-4">Login</button>
          <p class="mt-4 ml-5">
            Don't have an account yet?
            <RouterLink to="/register">Register</RouterLink>
          </p>
        </Form>
      </div>
    </div>
  </div>
</template>

<script>
import { Form, Field, ErrorMessage } from "vee-validate";
import { useUserStore } from "@/stores/UserStore";
export default {
  name: "Login",
  components: {
    Form,
    Field,
    ErrorMessage,
  },
  data() {
    return {
      name: "",
      email: "",
      password: "",
      loginError: null,
    };
  },
  methods: {
    login(values) {
      console.log(values);
      let data = { email: this.email, password: this.password };

      this.axios.get("/sanctum/csrf-cookie").then(() => {
          this.axios.post("/login", data).then(() => {
              const store = useUserStore();
              this.axios.get("/api/user").then((response) => {
                  store.user = response.data;
                  localStorage.setItem('authenticated', 'true')
                  this.$router.push({ path: "/" });
                })
                .catch((error) => {
                  console.log("login error")
                  console.log(error.response.status);
                });
            })
            .catch((error) => {
              if (
                error.response.status === 401 ||
                error.response.status === 422
              ) {
                this.loginError = "Incorrect email or password.";
              }
            });
        })
        .catch(error => {
          // Not really unknown, but there's not a lot of useful info we can provide to an end-user.
          // If this happens, the API could be down, or potentially we could have a CSRF token mismatch.
          this.loginError = "An unknown error has occurred. Please try again later."
        });
    },
    /**
     * @param value
     * @returns {string|boolean}
     */
    validateEmail(value) {
      if (!value) {
        return "The email field is required";
      }
      const regex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
      if (!regex.test(value)) {
        return "This field must be a valid email";
      }
      return true;
    },
    /**
     * @param value
     * @returns {string|boolean}
     */
    validatePassword(value) {
      if (!value) {
        return "The password field is required";
      }
      return true;
    },
  },
};
</script>

<style scoped></style>
