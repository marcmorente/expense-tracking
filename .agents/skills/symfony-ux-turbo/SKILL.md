---
name: symfony-ux-turbo
description: Uses Symfony UX Turbo components and PHP APIs for Turbo Frames, Streams, broadcasts, and page settings. Use when building or changing Turbo features in a Symfony UX application.
---

# Symfony UX Turbo

Assume that `symfony/ux-turbo` is installed and configured.

Do not import, start, or configure Turbo Core. Symfony UX Turbo already provides
it.

## Twig components

Symfony UX Turbo provides these Twig components:

- `<twig:Turbo:Frame>`
- `<twig:Turbo:Stream>`
- `<twig:Turbo:Stream:Append>`
- `<twig:Turbo:Stream:Prepend>`
- `<twig:Turbo:Stream:Replace>`
- `<twig:Turbo:Stream:Update>`
- `<twig:Turbo:Stream:Remove>`
- `<twig:Turbo:Stream:Before>`
- `<twig:Turbo:Stream:After>`
- `<twig:Turbo:Stream:Refresh>`
- `<twig:Turbo:Stream:From>`

Prefer these components over raw Frame and Stream elements.

Always use `<twig:Turbo:Stream:From>` for stream sources.

Do not use `turbo_stream_from()`, `turbo_stream_listen()`, or the
`mercure-turbo-stream` Stimulus controller.

## Symfony UX APIs

Use `TurboStreamResponse` only when PHP must compose several Stream actions
dynamically. Prefer Twig components for response markup.

Symfony UX Turbo provides these PHP constants:

- `TurboBundle::STREAM_FORMAT`
- `TurboBundle::STREAM_MEDIA_TYPE`

Symfony UX Turbo provides these Twig frame request functions:

- `turbo_is_frame_request()`
- `turbo_frame_request_id()`

## Page settings

Always write Turbo page settings as raw meta tags:

```html
<meta name="turbo-cache-control" content="no-cache" />
<meta name="turbo-visit-control" content="reload" />
<meta name="turbo-refresh-method" content="morph" />
<meta name="turbo-refresh-scroll" content="preserve" />
```

Do not use Symfony UX Twig helper functions for these meta tags.

Use the [Symfony UX Turbo documentation](https://symfony.com/bundles/ux-turbo/current/index.html) for integration details.

Use the [Hotwire Turbo documentation](https://turbo.hotwired.dev/) for Turbo behavior and API details.
