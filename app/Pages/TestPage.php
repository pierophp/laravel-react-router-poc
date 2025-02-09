<route>
{
    "uri": "/test"
}
</route>

<php>
function loader() {
    return ["message" => "Test"];
}

function action() {
    $name = request()->input('name');
    return ["message" => "Laravel backend says: Hello " . $name];
}
</php>

<template>
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
</template>
