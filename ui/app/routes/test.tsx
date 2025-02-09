import type { Route } from "./+types/home";
import { useActionData, Form } from "react-router";

export function meta({}: Route.MetaArgs) {
    return [
        { title: "Laravel 🤝 React Router" },
        { name: "description", content: "Laravel 🤝 React Router" },
    ];
}

export default function Home() {
    const actionData = useActionData();
    return (
        <main className="flex items-center justify-center pt-16 pb-4">
            <div className="flex-1 flex flex-col items-center gap-16 min-h-0">
                <div className="max-w-[300px] w-full space-y-6 px-4">
                    <nav className="rounded-3xl border border-gray-200 p-6 dark:border-gray-700 space-y-4">
                        <p className="leading-6 text-gray-700 dark:text-gray-200 text-center">
                            {actionData?.message}
                            <Form method="post">
                                <input type="text" name="name" />
                                <button type="submit">Submit</button>
                            </Form>
                        </p>
                    </nav>
                </div>
            </div>
        </main>
    );
}
export async function loader() {
const response = await fetch("http://127.0.0.1:8000/api/test");
return await response.json();
}
export async function action({ request }) {
const formData = await request.formData();
const response = await fetch("http://127.0.0.1:8000/api/test", {
method:"POST",
body: formData});
return await response.json();
}