import type { Route } from "./+types/home";
import { useLoaderData } from "react-router";

export function meta({}: Route.MetaArgs) {
    return [
        { title: "Laravel 🤝 React Router" },
        { name: "description", content: "Laravel 🤝 React Router" },
    ];
}

export default function Home() {
    const { message } = useLoaderData();
    return (
        <main className="flex items-center justify-center pt-16 pb-4">
            <div className="flex-1 flex flex-col items-center gap-16 min-h-0">
                <h1 class="text-4xl font-bold text-center text-blue-400">
                    {message}
                </h1>
            </div>
        </main>
    );
}
export async function clientLoader() {
const response = await fetch("http://127.0.0.1:8000/api/");
return await response.json();
}
export async function clientAction({ request }) {
const formData = await request.formData();
const response = await fetch("http://127.0.0.1:8000/api/", {
method:"POST",
body: formData});
return await response.json();
}