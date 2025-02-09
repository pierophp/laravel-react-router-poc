<route>
{
    "uri": "/"
}
</route>

<php>
function loader() {
    return ["message" => "Hello World From Laravel Loader"];
}
</php>

<template>
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
</template>
