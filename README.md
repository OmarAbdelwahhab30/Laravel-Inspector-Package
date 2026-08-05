# Laravel Inspector

**See exactly what your Laravel app did behind every HTTP request** — the route, the controller, the SQL queries, the events, the queued jobs, and a timeline of it all.

You can look at that information in two places:

1. **A web dashboard** built into your app at `/__devtools` (nothing to install, works in any browser).
2. **A Chrome DevTools panel** — click any request in the DevTools *Laravel* tab and see the backend story behind it.

> ⚠️ This is a **local development tool only**. It refuses to turn on unless you are in `local` environment with debug mode on **and** you explicitly opt in. It can never run on your production server. See [Is this safe?](#is-this-safe).

---

## Table of contents

- [What you get](#what-you-get)
- [Requirements](#requirements)
- [Step 1 — Install the package](#step-1--install-the-package)
- [Step 2 — Turn it on](#step-2--turn-it-on)
- [Step 3 — Open the dashboard](#step-3--open-the-dashboard)
- [Step 4 (optional) — Install the Chrome extension](#step-4-optional--install-the-chrome-extension)
- [Configuration file explained](#configuration-file-explained)
- [Click a file, open your editor](#click-a-file-open-your-editor)
- [Cleaning up old data](#cleaning-up-old-data)
- [All the URLs this package adds](#all-the-urls-this-package-adds)
- [Is this safe?](#is-this-safe)
- [Troubleshooting](#troubleshooting)
- [Uninstalling](#uninstalling)
- [License](#license)

---

## What you get

For every request your app handles, Laravel Inspector saves a small "snapshot" file containing:

| Tab in the UI | What it shows |
| --- | --- |
| **Overview** | HTTP method, URL, matched route (URI + name), controller class, method, file and line, response status, total duration |
| **Queries** | Every SQL query, its bindings, how long it took, which connection, and the exact file + line that fired it. Slow queries are flagged in red |
| **Events** | Every event your app fired (Laravel's own internal events are filtered out), with the payload and the file + line |
| **Jobs** | Every job pushed to a queue during the request, with its connection, queue name and status |
| **Timeline** | A visual bar chart of when each query / event / job happened inside the request |

Every request also gets a response header, `X-Laravel-Devtools-Request`, containing its snapshot ID. That is how the Chrome extension links a network request to its backend snapshot.

---

## Requirements

| Thing | Version |
| --- | --- |
| PHP | 8.1 or newer |
| Laravel | 10, 11 or 12 |
| Browser (for the extension only) | Google Chrome / Edge / Brave |

---

## Step 1 — Install the package

Open a terminal inside your Laravel project and run:

```bash
composer require omarabdulwahhab/laravel-inspector --dev
```

The `--dev` flag means the package is installed **only on your machine**, never on your production server. That is exactly what you want.

That is the whole install. Laravel auto-discovers the package — you do **not** need to register any service provider by hand.

<details>
<summary>Installing from a local folder instead (for contributors)</summary>

If you cloned this repository and want to use your local copy, add this to your app's `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "../Laravel-Inspector/package"
    }
]
```

Then run:

```bash
composer require omarabdulwahhab/laravel-inspector:@dev --dev
```
</details>

---

## Step 2 — Turn it on

The package stays **completely asleep** until you switch it on. Open your project's `.env` file and make sure these three lines are present:

```env
APP_ENV=local
APP_DEBUG=true
LARAVEL_DEVTOOLS_ENABLED=true
```

**All three are required.** If any one of them is missing or different, the package registers nothing at all — no routes, no listeners, no middleware, zero overhead.

Now clear the config cache so Laravel notices the change:

```bash
php artisan config:clear
```

Done. Load any page of your app and Laravel Inspector starts recording.

---

## Step 3 — Open the dashboard

Visit this URL in your browser (replace the domain with your own):

```
http://your-app.test/__devtools
```

You will see a live list of the requests your app is handling. It refreshes itself every 2 seconds. Click any request to inspect it.

> Tip: keep this open in a second browser tab while you click around your app in the first one.

---

## Step 4 (optional) — Install the Chrome extension

Everything above already works in your browser at `/__devtools`. This step is only if you'd rather inspect requests from **inside Chrome DevTools**, next to the Network and Console tabs.

The extension is not on the Chrome Web Store — you add it yourself in about a minute. It is **not** part of the Composer package, so download it first.

### 1. Download the extension

**[⬇ Download the ZIP](https://github.com/OmarAbdelwahhab30/Laravel-Inspector-Extension/archive/refs/heads/main.zip)** and unzip it anywhere you like — your Desktop is fine.

Prefer git? Then:

```bash
git clone https://github.com/OmarAbdelwahhab30/Laravel-Inspector-Extension.git0
```

> Keep the folder somewhere permanent. Chrome loads the extension from this folder every time it starts — if you delete or move it, the extension disappears.

### 2. Add it to Chrome

1. Open a new tab and go to `chrome://extensions`.
2. Turn on **Developer mode** — the switch in the **top-right** corner.
3. Three buttons appear on the left. Click **Load unpacked**.
4. Pick the **`extension`** folder you just downloaded, and click **Select Folder**.

Laravel Inspector now shows up in your extensions list. Done.

> Chrome may show a "Disable developer mode extensions" warning when it starts. That's normal for any extension installed this way — just close it.

### 3. Use it

1. Open any page of your Laravel app.
2. Press `F12` to open DevTools.
3. Click the **Laravel Inspector** tab at the top (it may be hidden behind the `»` arrow if your window is narrow).
4. Refresh the page.

Requests that Laravel Inspector recorded show a small red dot in the list on the left. Click one to see its snapshot on the right.

### Updating later

Download the new version over the old folder, then go back to `chrome://extensions` and click the **↻ reload** icon on the Laravel Inspector card.

> Works in any Chromium browser — Chrome, Edge, Brave, Opera and Arc all have the same `Developer mode` → `Load unpacked` flow.

---

## Configuration file explained

You only need this step if you want to change the defaults. Publish the config file:

```bash
php artisan vendor:publish --tag=devtools-config
```

This creates **`config/devtools.php`** in your project. Here it is in full, with an explanation of every setting:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Env Var
    |--------------------------------------------------------------------------
    |
    | The name of the .env variable that switches the package on. Only change
    | this if the name LARAVEL_DEVTOOLS_ENABLED clashes with something else
    | in your app. If you change it here, use the new name in your .env too.
    |
    */
    'enabled_env_var' => 'LARAVEL_DEVTOOLS_ENABLED',

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | Where the snapshots are saved and how long they live.
    |
    | driver ............... Only 'file' is supported today. Any other value
    |                        throws an error when the app boots.
    | path ................. Folder for the snapshot .json files. The folder is
    |                        created automatically and a .gitignore is dropped
    |                        inside it, so nothing is ever committed to git.
    | prune_after_minutes .. Snapshots older than this get deleted automatically.
    |                        Lower it if the folder grows too fast.
    |
    */
    'storage' => [
        'driver' => 'file',
        'path' => storage_path('devtools'),
        'prune_after_minutes' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore Paths
    |--------------------------------------------------------------------------
    |
    | Requests to these paths are NOT recorded — useful for noisy things like
    | asset requests and polling endpoints. Wildcards (*) are supported, and
    | the patterns are matched the same way as $request->is().
    |
    | Heads up: 'dashboard/*' is in the default list. If your own app has
    | /dashboard pages that you DO want to inspect, delete that line.
    |
    */
    'ignore_paths' => [
        '__devtools',
        '__devtools/*',
        '_debugbar/*',
        'storage/*',
        'assets/*',
        'ajax/*',
        'favicon.ico',
        'build/*',
        'dashboard/*',
        'sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | Collectors
    |--------------------------------------------------------------------------
    |
    | Turn individual pieces of data collection on or off. Set one to false and
    | that tab simply stays empty — handy if, for example, a chatty event
    | listener is making your snapshots huge.
    |
    | route ....... HTTP method, URL, matched route URI and route name
    | controller .. Controller class, method, file path and line number
    | response .... HTTP status code and total request duration
    | query ....... Every SQL query with bindings, duration and origin
    | event ....... Every application event fired during the request
    | job ......... Every job pushed onto a queue during the request
    |
    */
    'collectors' => [
        'route' => true,
        'controller' => true,
        'response' => true,
        'query' => true,
        'event' => true,
        'job' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Slow Query Threshold
    |--------------------------------------------------------------------------
    |
    | Any query slower than this many milliseconds is flagged as "slow" in the
    | UI so it stands out. Lower it to be stricter about performance.
    |
    */
    'slow_query_threshold' => 50,

    /*
    |--------------------------------------------------------------------------
    | Redacted Bindings
    |--------------------------------------------------------------------------
    |
    | Snapshots are saved as plain-text JSON files on your disk, so passwords
    | and tokens must never end up inside them. Any query binding whose column
    | name matches one of these patterns is stored as "[REDACTED]" instead of
    | its real value. Add your own sensitive column names here.
    |
    */
    'redact_bindings' => [
        '*password*',
        '*passwd*',
        '*secret*',
        '*token*',
        '*api_key*',
        '*apikey*',
        '*access_key*',
        '*private_key*',
        '*credit_card*',
        '*card_number*',
        '*cvv*',
        '*ssn*',
    ],

];
```

After editing the config, run:

```bash
php artisan config:clear
```

---

## Click a file, open your editor

In the UI, file paths (the controller, the line that ran a query, the line that fired an event) are clickable. Clicking one **opens that exact file at that exact line in your code editor**.

There is nothing to configure — the package looks at the programs currently running on your machine and picks the first editor it recognises:

VS Code · VS Code Insiders · Cursor · Antigravity IDE · PhpStorm · IntelliJ IDEA · WebStorm · PyCharm · Sublime Text · Atom · Notepad++

**If your editor is not in that list** (or it is a terminal editor), tell the package which command to run by adding one line to `.env`:

```env
LARAVEL_DEVTOOLS_EDITOR=nvim
```

The value is just the command name you would type in a terminal to open that editor.

> This works because in local development the machine running PHP is your own machine — the same desktop your browser is on.

---

## Cleaning up old data

Snapshots live in `storage/devtools/` as small `.json` files, and they clean themselves up: on roughly 1 in 10 requests the package deletes anything older than `prune_after_minutes` (20 by default).

If you ever want to clear them by hand:

```bash
php artisan devtools:prune
```

That deletes snapshots older than 60 minutes. To choose a different age:

```bash
php artisan devtools:prune --older-than=5
```

To wipe everything right now, use `--older-than=0`.

---

## All the URLs this package adds

These only exist while the package is enabled. Everything else in your app is untouched.

| URL | What it does |
| --- | --- |
| `GET /__devtools` | The web dashboard (HTML page) |
| `GET /__devtools/latest` | The 50 most recent snapshots, as JSON — this is what the dashboard polls |
| `GET /__devtools/request/{id}` | One full snapshot, as JSON |
| `GET /__devtools/open-editor?file=...&line=...` | Opens a file in your editor |

---

## Is this safe?

Yes — the package is built so it **cannot** run outside local development:

- **Triple lock.** It only activates when `LARAVEL_DEVTOOLS_ENABLED=true` **and** `APP_DEBUG=true` **and** `APP_ENV=local`. Miss one and the service provider returns immediately: no routes, no middleware, no listeners registered at all.
- **Checked twice.** Every route also re-checks the same three conditions when it is hit, and returns a 404 if they no longer hold.
- **Installed as a dev dependency.** With `--dev`, the package is not even present on a production server (`composer install --no-dev`).
- **Secrets are redacted.** Query bindings for password/token/card-style columns are replaced with `[REDACTED]` before anything is written to disk.
- **Data never reaches git.** The storage folder gets its own `.gitignore` automatically the first time it is written to.
- **IDs cannot be guessed or abused.** Snapshot IDs are server-generated UUIDs, and the endpoint rejects anything that is not a well-formed UUID.

Still — the snapshots do contain your SQL and your route data in plain text on your disk. Treat `storage/devtools/` like any other local debug output.

---

## Troubleshooting

**The dashboard shows a 404 page.**
The package is not enabled. Check that all three lines are in `.env` (`APP_ENV=local`, `APP_DEBUG=true`, `LARAVEL_DEVTOOLS_ENABLED=true`), then run `php artisan config:clear`. If you ever ran `php artisan config:cache`, run `php artisan config:clear` again.

**The dashboard loads but the list stays empty.**
Open a page of your app in another tab to generate traffic. If it is still empty, check that the path you are visiting is not in the `ignore_paths` list (remember `dashboard/*` is ignored by default). Also confirm that `storage/devtools/` is writable.

**The Chrome panel only shows requests made after I opened DevTools.**
The extension ships with access to the usual local dev hosts (`localhost`, `127.0.0.1`, `*.localhost`, `*.test`). If your site runs on a different domain — `myapp.local`, a `*.ddev.site` address, a custom host — grant access to it: `chrome://extensions` → **Laravel Inspector** → **Details** → **Site access** → **On all sites**. Everything else in the panel works either way; only the older requests are affected.

**The Chrome panel says "Snapshot not found".**
The extension is talking to a domain where the package is not enabled, or the snapshot has already been pruned. Check the `.env` on that domain, and raise `prune_after_minutes` if you inspect requests long after making them.

**The Queries / Events / Jobs tabs are empty.**
Make sure the matching collector is `true` in `config/devtools.php`. Note that jobs are only recorded when they are *pushed to a queue* during the request.

**Clicking a file does nothing.**
No supported editor was detected as running. Open your editor first, or set `LARAVEL_DEVTOOLS_EDITOR` in `.env` as shown above.

**"Unsupported devtools storage driver" error on boot.**
`storage.driver` in `config/devtools.php` must be exactly `'file'`. It is the only driver available.

---

## Uninstalling

```bash
composer remove omarabdulwahhab/laravel-inspector
```

Then delete the leftovers if you want a clean slate:

- `config/devtools.php`
- `storage/devtools/`
- the `LARAVEL_DEVTOOLS_ENABLED` line in `.env`

---

## License

MIT. See [LICENSE](LICENSE).
