<route>
{
    "uri": "/"
}
</route>

<php-action>
    return ["message" => "Hello World From Laravel Action"];
</php-action>

<php-loader>
    return ["message" => "Hello World From Laravel Loader"];
</php-loader>

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
                <div className="max-w-[300px] w-full space-y-6 px-4">
                    <nav className="rounded-3xl border border-gray-200 p-6 dark:border-gray-700 space-y-4">
                        <p className="leading-6 text-gray-700 dark:text-gray-200 text-center">
                            {message}
                        </p>
                    </nav>
                </div>
            </div>
        </main>
    );
}
</template>
