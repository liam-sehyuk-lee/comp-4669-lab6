import { createRouter, createWebHistory } from "vue-router";
import ChatDashboard from "@/views/ChatDashboard.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: ChatDashboard,
    },
    {
      path: "/posts",
      name: "posts",
      component: () => import("../views/Posts.vue"),
    },
    {
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      path: "/login",
      name: "login",
      component: () => import("../views/Login.vue"),
    },
    {
      path: "/register",
      name: "register",
      component: () => import("../views/Register.vue"),
    },
  ],
});

const unauthenticatedRoutes = ["login", "register"];

/**
 * With 2 variables being used in routing decision-making, we have 2^2 cases to handle:
 *
 * 1. unauthenticated user accessing unauthenticated pages (login, register): allowed
 * 2. authenticated user accessing unauthenticated pages: not allowed -> go to home page
 * 3. unauthenticated user accessing authenticated pages: not allowed -> go to the login pge
 * 4. authenticated user accessing authenticated pages: allowed
 *
 * The beforeEach callback is responsible for ensuring this functionality.
 */
router.beforeEach((to, from, next) => {
  const goingToUnauthenticatedRoute = unauthenticatedRoutes.includes(to.name);
  const authenticated = localStorage.getItem('authenticated') === 'true'

  if (authenticated && goingToUnauthenticatedRoute) {
    next({name: 'home'});
  } else if (!authenticated && !goingToUnauthenticatedRoute) {
    next({name: 'login'});
  } else {
    next();
  }

});

export default router;
