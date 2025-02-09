import { type RouteConfig, route } from "@react-router/dev/routes";

export default [
	route("/", "routes/test.tsx"),
	route("/test", "routes/test.tsx")
] satisfies RouteConfig;
