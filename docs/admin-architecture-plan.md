# WordPress Plugin Admin Architecture Plan

## 1) Plugin Structure Health Check (Current vs. Target)

### Current observations
- The plugin already separates concerns into `admin/`, `includes/`, and `public/`, which is a good foundation.
- Admin UI was previously monolithic (`admin/partials/admin-display.php`) and all platforms were edited in one long page.
- Data persistence had limited validation and no dedicated global settings model.

### Target improvements
- Parent menu + dedicated submenu pages for each platform and settings.
- Per-platform independent editing controls.
- Per-platform question CRUD (add/delete + editable content fields).
- Dedicated global settings model and page.
- Backward compatibility by migrating old platform arrays to a scalable keyed structure.

## 2) Admin Menu Architecture

### Parent menu
- **Social Branding** (`admin.php?page=coachpro-ai-teacher-social-branding`)

### Submenus
1. **Dashboard**
2. **Facebook**
3. **YouTube**
4. **Instagram**
5. **TikTok**
6. **Settings**

This menu model scales cleanly: add a new platform slug and register a submenu dynamically.

## 3) Page-by-Page Field/Control Design

## Dashboard page
- Platform status table
- Enabled/disabled visibility
- Question counts
- Quick links to each platform editor

## Platform page (Facebook/YouTube/Instagram/TikTok)
- Enable/disable toggle
- Platform title
- Platform description
- Theme color
- Button label
- Question bank editor (CRUD)
  - Question text EN/UR
  - Instruction title EN/UR
  - Instruction steps EN/UR (line-separated)
  - Instruction tips EN/UR (line-separated)
  - Tool/embed HTML field
- Add question button
- Delete last question button

## Global Settings page
- Dashboard title
- Default language (EN/UR)
- Primary color
- Items per page
- Show branding toggle

## 4) Data/Settings Model

## Option: `cpai_tsb_platforms`
Associative array keyed by platform slug:
- `id`
- `name_en`
- `name_ur`
- `icon`
- `enabled`
- `title`
- `description`
- `color`
- `button_label`
- `questions[]`
  - `id`
  - `text_en`
  - `text_ur`
  - `instruction_en` (`title`, `steps[]`, `tips[]`, `tool`)
  - `instruction_ur` (`title`, `steps[]`, `tips[]`, `tool`)

## Option: `cpai_tsb_settings`
- `default_language`
- `show_branding`
- `items_per_page`
- `dashboard_title`
- `primary_color`

### Migration strategy
- Detect legacy indexed platform list and transform to keyed structure by platform slug.
- Preserve existing question content.

## 5) Recommended File Structure

```text
admin/
  class-cpai-tsb-admin.php
  partials/
    page-dashboard.php
    page-platform.php
    page-settings.php
includes/
  class-cpai-tsb.php
  class-cpai-tsb-activator.php
public/
  class-cpai-tsb-public.php
docs/
  admin-architecture-plan.md
```

## 6) Implementation Steps

1. Build parent menu and submenu routing in `CPAI_TSB_Admin::add_plugin_admin_menu()`.
2. Create dedicated render methods for dashboard, platform, and settings pages.
3. Introduce action-based admin post handlers (`save_platform`, `add_question`, `delete_question`, `save_settings`).
4. Implement strong sanitization for all incoming fields.
5. Add migration helper for legacy platform data.
6. Add defaults scaffolding for platform records and question records.
7. Update activator to seed platform + settings options.
8. Keep frontend compatible by converting keyed admin model to frontend list in public class.
9. Validate via PHP linting and review menu/page flow.

## 7) Scalability Notes

- New platforms can be introduced by adding a slug to the platform list and defaults map.
- Future per-platform advanced modules (AI prompts, template libraries, analytics toggles) can be nested under each platform record without schema breaks.
- The architecture supports eventually moving question records to a custom post type or custom DB table if scale grows.
