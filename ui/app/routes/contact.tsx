import type { Route } from "./+types/home";
import { useLoaderData, useActionData, Form } from "react-router";

export function meta({}: Route.MetaArgs) {
    return [
        { title: "Laravel 🤝 React Router" },
        { name: "description", content: "Laravel 🤝 React Router" },
    ];
}

export default function Home() {
    const actionData = useActionData();
    const { title } = useLoaderData();

    return (
        <main className="flex items-center justify-center pt-16 pb-4">
            <div className="flex-1 flex flex-col items-center gap-16 min-h-0">
                <div className="max-w-[600px] w-full space-y-6 px-4">
                    <nav className="rounded-3xl border border-gray-200 p-6 dark:border-gray-700 space-y-4">
                        <h1 class="text-4xl font-bold text-center text-blue-400">
                            { title }
                        </h1>
                        <Form method="post">
                            <div class="flex flex-col space-y-2">
                            <label for="input" class="text-lg font-medium text-gray-300">Name</label>
                            <input
                                id="input"
                                type="text"
                                placeholder="Enter your name"
                                name="name"
                                class="w-full px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                            />
                            </div>
                            <button
                                type="submit"
                                class="w-full px-6 py-3 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all"
                                >
                                Submit
                            </button>
                        </Form>
                        {actionData?.message && (
                            <div class="w-full max-w-md p-4 text-white bg-green-500 rounded-lg shadow-md flex items-center space-x-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-lg">{actionData?.message}</span>
                            </div>
                        )}

                    </nav>
                </div>
            </div>
        </main>
    );
}
export async function loader() {
const response = await fetch("http://127.0.0.1:8000/api/contact");
return await response.json();
}
export async function action({ request }) {
const formData = await request.formData();
const response = await fetch("http://127.0.0.1:8000/api/contact", {
method:"POST",
body: formData});
return await response.json();
}