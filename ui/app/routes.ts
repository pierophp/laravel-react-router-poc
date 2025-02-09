import { type RouteConfig, route } from "@react-router/dev/routes";

export default [
	route("/contact", "routes/contact.tsx"),
	route("/", "routes/home.tsx")
] satisfies RouteConfig;
