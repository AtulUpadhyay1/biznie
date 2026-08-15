# Biznie Admin — Design System

Single source of truth for the admin panel UI. Every screen must follow this.
All visual values live in `public/admin_css/assets/css/biznie-admin.css` as CSS
custom properties. **Never hard-code a colour, radius, shadow or font in a Blade
file.**

---

## 1. Hard rules

1. **No `<style>` blocks in Blade files.** If a screen needs a new visual
   pattern, add a class to `biznie-admin.css` instead.
2. **No inline `style="..."` with colours, borders, radii, shadows or fonts.**
   Layout-only inline styles (`style="width:120px"` on a table column) are
   tolerated but a utility class is preferred.
3. **No hard-coded hex colours** anywhere in Blade. Use the semantic classes
   below.
4. **Icons are Bootstrap Icons (`bi bi-*`) only.** No `fa fa-*`, no
   `data-feather` (the feather webfont is not reachable).
5. **One primary action per screen.** Everything else is secondary.

---

## 2. Buttons

| Purpose | Class | Example |
|---|---|---|
| Primary action (Save, Add, Update, Approve) | `btn btn-danger btn-sm` | `Add Brand` |
| Secondary action (Back, Cancel, View all, filters) | `btn btn-secondary btn-sm` | `Back` |
| Destructive confirm inside a menu/modal | `btn btn-danger btn-sm` | `Delete` |
| Soft/tonal action | `btn btn-sm btn-inverse-primary` | `Add field` |
| Semantic outline (rare) | `btn btn-sm btn-outline-success` | `View message` |

- Always `btn-sm` inside cards, tables and toolbars. Plain `btn` only for
  full-width form submits.
- Icon goes **inside** the button, before the label: `<i class="bi bi-plus-lg"></i>Add`.
  The `.btn` is a flexbox with a gap — do **not** add `me-1`/`btn-icon-prepend`
  spacing classes.
- Drop legacy classes: `btn-icon-text`, `btn-icon-prepend`, `align-items-center`.
- Use the shared components where they fit:
  `<x-submit-btn text="Save" />`, `<x-cancel-btn text="Cancel" function="..." />`,
  `<x-add-btn text="Add Row" function="..." />`.

---

## 3. Page structure

Every list/form screen is one or more `.card`s inside the Livewire root `<div>`:

```blade
<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="d-flex align-items-center gap-2">
                        {{-- search / filters / primary action --}}
                    </div>
                </div>
                <div class="card-body">
                    {{-- content --}}
                </div>
            </div>
        </div>
    </div>
</div>
```

For screens that need a title above the cards (dashboards, overviews):

```blade
<div class="bz-page-head">
    <div>
        <h1 class="bz-page-head__title">{{ $page_title }}</h1>
        <p class="bz-page-head__sub">One line explaining the screen.</p>
    </div>
    <div class="bz-page-head__actions">{{-- buttons --}}</div>
</div>
```

Never use a bare `<h2 class="fw-bold text-primary">` hero.

---

## 3b. Card-header toolbar — MANDATORY for list screens

Search, filters and the primary action must sit on **one row**. Bootstrap gives
`.form-control` / `.form-select` `width: 100%`, so dropping them straight into a
flex row makes each one claim the whole line and the toolbar stacks. Always use
`.bz-toolbar`:

```blade
<div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <h4>{{ $page_title }}</h4>

    <div class="bz-toolbar">
        <div class="custom-search-bar">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <label class="bz-filter-label" for="search">Search</label>
                <input id="search" type="text" class="form-control" placeholder="Search here…"
                       wire:model.live.debounce.400ms="search">
            </div>
        </div>

        <label class="bz-filter-label" for="status">Status</label>
        <select id="status" class="form-select" wire:model.live="status">…</select>

        <a href="…" class="btn btn-danger btn-sm" wire:navigate>
            <i class="bi bi-plus-lg"></i>Add
        </a>
    </div>
</div>
```

`.bz-toolbar` sizes every child to 34px, caps select/input widths, and stacks
them full-width below 576px. Do **not** add `float-end`, `w-25`, `ms-auto` or
inline `max-width` to toolbar children.

---

## 3c. Control sizes

One scale, driven by tokens — never set `height`, `padding` or `font-size` on a
control in Blade.

| Token | Value | Applies to |
|---|---|---|
| `--bz-control-h` | 36px | `.form-control`, `.form-select`, `.btn`, select2 |
| `--bz-control-h-sm` | 31px | `.form-control-sm`, `.form-select-sm`, `.btn-sm` |
| `--bz-control-h-lg` | 44px | `.form-control-lg`, `.btn-lg` |

Inside `.bz-toolbar` everything is normalised to 34px regardless of `-sm`.
Body form fields use the default (36px) — do **not** sprinkle `form-control-sm`
on ordinary form fields; the default is already compact.

---

## 4. Tables

```blade
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th style="width:120px">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($list as $item)
                <tr wire:key="row-{{ $item->id }}">
                    <td>{{ $list->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="bz-cell-title">{{ $item->name }}</div>
                        <div class="bz-cell-sub">{{ $item->subtitle }}</div>
                    </td>
                    <td>…</td>
                </tr>
            @empty
                <x-table-no-data />
            @endforelse
        </tbody>
    </table>
</div>
```

- Prefer `class="table table-hover"`. `class="custom-table"` is legacy but
  styled identically — leave it alone rather than doing a risky rewrite.
- Header cells are automatically uppercase micro-labels. Do not add
  `text-uppercase`, `fw-bold` or font-size classes.
- Two-line cells use `.bz-cell-title` + `.bz-cell-sub`.
- The `<b>Label:</b> value` pattern renders as an uppercase micro-label — keep it.
- Empty state is always `<x-table-no-data />`. Never an ad-hoc "No data found" row.
- Row numbering must survive pagination: `{{ $list->firstItem() + $loop->index }}`,
  not `{{ $loop->iteration }}`.
- Row actions go in a `⋮` dropdown; the caret is hidden automatically inside `td`.
- A key/value `<table>` nested inside a `<td>` is rendered compactly and
  borderless automatically — do **not** add `table-sm`/`table-bordered` hoping to
  shrink it, and never set padding on those cells. `.bz-kv-list` is preferred for
  new code.
- `.form-floating` is not part of the system (its 54px control breaks the size
  scale). Use `<label class="form-label">` above the control instead.

---

## 5. Forms

```blade
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input type="text" id="name" wire:model="name"
               class="form-control @error('name') is-invalid @enderror"
               placeholder="Enter name">
        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>
```

- Every control has a `<label class="form-label">` with a matching `for`/`id`.
  Placeholder-only fields are not acceptable.
- Column spacing is `mb-3` — not `mb-2`, `mb-4` or `mt-*`.
- Never set `height`, `padding`, `font-size` or `border` on `.form-control`.
- Selects: `form-select`, or `select2` for searchable ones — both are themed.
- Checkboxes/switches: `form-check` / `form-check form-switch`.
- Submit row sits at the bottom of the `card-body`, primary action first.

---

## 6. Status & badges

| Meaning | Markup |
|---|---|
| Approved / active / paid | `<span class="bz-status bz-status--success">Approved</span>` |
| Pending / awaiting | `<span class="bz-status bz-status--warning">Pending</span>` |
| Rejected / failed | `<span class="bz-status bz-status--danger">Rejected</span>` |
| Informational | `<span class="bz-status bz-status--info">Draft</span>` |
| Neutral / inactive | `<span class="bz-status bz-status--muted">Inactive</span>` |

`<span class="badge bg-success">` etc. still work (soft-toned automatically) and
are fine for counts. Use `.bz-status` for record state.

---

## 6b. Surfaces, toolbars & small patterns

Use these instead of stacking Bootstrap utilities.

| Instead of | Use |
|---|---|
| `<div class="p-3 border rounded">` (read-only box) | `<div class="bz-panel">` |
| `<div class="p-3 border rounded bg-white">` | `<div class="bz-panel bz-panel--plain">` |
| `Label : value <br>` blocks, or nested `table-sm table-bordered` in a cell | `.bz-kv-list` with `<div><dt>Label</dt><dd>Value</dd></div>` |
| `d-flex justify-content-between border rounded p-3` + a switch | `<div class="bz-toggle-row">` |
| `style="max-width:220px"` / `w-25` on a toolbar input | `<div class="bz-toolbar-field">` (`--sm` / `--lg`) |
| A toolbar control with no label | wrap the label in `class="bz-filter-label"` (visually hidden) |
| `btn-danger` + `btn-outline-danger` used as a tab strip | `.bz-tabs` > `.bz-tab` / `.bz-tab.is-active` |
| `<a><i class="bi bi-three-dots-vertical icon-lg"></i></a>` | `<a class="bz-row-action"><i class="bi bi-three-dots-vertical"></i></a>` |
| `wd-70 rounded-circle` on a photo | `bz-avatar-img` (`--lg` 72px, `--xl` 96px) |
| a 60px coloured circle in a modal header | `.bz-icon-circle` (`--success`/`--warning`/`--muted`) |
| an absolutely-positioned delete "x" on a thumbnail | `.bz-thumb` > `.bz-thumb-remove` |
| `float-end` / `d-flex justify-content-end` around a paginator | `<div class="bz-pagination">` |
| `mt-4` between two blocks in a card | `.bz-section-gap` |
| an icon-only button | add `btn-icon` to the `btn` |

`.bz-stat.is-active` marks a stat tile that is acting as a selected toggle.
`.bz-status--secondary` is an alias of `.bz-status--muted`.

---

## 7. Utility classes available

- `.bz-page-head`, `.bz-page-head__title`, `.bz-page-head__sub`, `.bz-page-head__actions`
- `.bz-section-label` — uppercase label above a group
- `.bz-card-sub` — sub-line under a card title
- `.bz-cell-title`, `.bz-cell-sub` — two-line table cells
- `.bz-stat-grid`, `.bz-stat` (+ `--brand|blue|green|amber|violet|cyan|pink|slate`)
- `.bz-metric-list` — label + number + chevron rows
- `.bz-empty`, `.bz-empty__icon`, `.bz-empty__title`, `.bz-empty__text`
- `.bz-status--*`, `.bz-chip`, `.bz-row-card`
- `.wz-step*`, `.wz-kv*`, `.sr-*` — wizard / request-detail patterns
- `.bz-spinner`, `.bz-spinner--sm`
- `.bz-num` — tabular figures for money and counts

---

## 8. Legacy classes to remove on sight

| Remove | Replace with |
|---|---|
| `btn-icon-text`, `btn-icon-prepend` | nothing (`.btn` has a gap) |
| `tx-10` … `tx-20` | `small` or nothing |
| `wd-30`, `ht-30`, `wd-80`, `ht-80` | a `.bz-*` class or leave sizing to CSS |
| `icon-md`, `icon-sm` | nothing |
| `float-end` on toolbar buttons | flex on the parent (`d-flex … justify-content-end`) |
| `bg-light`, `bg-gradient-primary` on headers | plain `.card-header` |
| `text-primary` used as a heading colour | nothing (headings are already themed) |
| `border-red`, `span-bd` | `.bz-row-card`, `.bz-chip` |

---

## 9. Tokens (reference — use the classes, not the values)

Brand `#C81E1E` · surface `#FFFFFF` · page `#F4F6F8` · border `#E4E8EE`
text `#111827` · muted `#6B7280` · success `#0E9F6E` · warning `#C2760C`
info `#2563EB` · sidebar `#0F172A` · font **Inter** · radius 8/10/12/16px
