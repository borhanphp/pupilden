# Unique Constraints Migration Summary

This document summarizes all the migrations created to update unique constraints to be composite with `organization_id`.

## Created Migration Files

### 1. **Courses Table**
- **File**: `2025_11_11_182712_add_unique_constraint_to_courses_table.php`
- **Column**: `slug`
- **New Constraint**: `organization_id + slug`
- **Constraint Name**: `courses_organization_slug_unique`

### 2. **Coupons Table**
- **File**: `2025_11_11_190328_add_unique_constraint_to_coupons_table.php`
- **Column**: `code`
- **New Constraint**: `organization_id + code`
- **Constraint Name**: `coupons_organization_code_unique`

### 3. **Course Categories Table**
- **File**: `2025_11_11_190341_add_unique_constraint_to_course_categories_table.php`
- **Column**: `slug`
- **New Constraint**: `organization_id + slug`
- **Constraint Name**: `course_categories_organization_slug_unique`

### 4. **Course Sub Categories Table**
- **File**: `2025_11_11_190354_add_unique_constraint_to_course_sub_categories_table.php`
- **Column**: `slug`
- **New Constraint**: `organization_id + slug`
- **Constraint Name**: `course_sub_categories_organization_slug_unique`

### 5. **Domains Table**
- **File**: `2025_11_11_190405_add_unique_constraint_to_domains_table.php`
- **Column**: `domain_name`
- **New Constraint**: `organization_id + domain_name`
- **Constraint Name**: `domains_organization_domain_name_unique`

### 6. **Users Table**
- **File**: `2025_11_11_190419_add_unique_constraint_to_users_table.php`
- **Column**: `email`
- **New Constraint**: `organization_id + email`
- **Constraint Name**: `users_organization_email_unique`

## Tables NOT Modified (Intentionally)

The following tables were found with unique constraints but were NOT modified because they should remain globally unique:

### 1. **Organizations Table**
- **Column**: `slug`
- **Reason**: Root table - slug must be globally unique across all organizations

### 2. **Personal Access Tokens Table**
- **Column**: `token`
- **Reason**: Security tokens must be globally unique

### 3. **Certificates Table**
- **Column**: `certificate_code`
- **Reason**: Certificate codes should be globally unique for verification purposes

### 4. **Failed Jobs Table**
- **Column**: `uuid`
- **Reason**: UUIDs must be globally unique by design

## Before Running Migrations

⚠️ **IMPORTANT**: Before running these migrations, check for duplicate data within each organization:

```sql
-- Check for duplicates in courses
SELECT organization_id, slug, COUNT(*) 
FROM courses 
GROUP BY organization_id, slug 
HAVING COUNT(*) > 1;

-- Check for duplicates in coupons
SELECT organization_id, code, COUNT(*) 
FROM coupons 
GROUP BY organization_id, code 
HAVING COUNT(*) > 1;

-- Check for duplicates in course_categories
SELECT organization_id, slug, COUNT(*) 
FROM course_categories 
GROUP BY organization_id, slug 
HAVING COUNT(*) > 1;

-- Check for duplicates in course_sub_categories
SELECT organization_id, slug, COUNT(*) 
FROM course_sub_categories 
GROUP BY organization_id, slug 
HAVING COUNT(*) > 1;

-- Check for duplicates in domains
SELECT organization_id, domain_name, COUNT(*) 
FROM domains 
GROUP BY organization_id, domain_name 
HAVING COUNT(*) > 1;

-- Check for duplicates in users
SELECT organization_id, email, COUNT(*) 
FROM users 
GROUP BY organization_id, email 
HAVING COUNT(*) > 1;
```

If any duplicates are found, you must clean them up before running the migrations.

## Running the Migrations

Once you've verified there are no duplicates, run:

```bash
php artisan migrate
```

## Rolling Back

If you need to rollback these changes:

```bash
php artisan migrate:rollback --step=6
```

This will revert all 6 migration files and restore the original unique constraints.

## Impact

After these migrations:
- ✅ Different organizations can use the same slugs/codes/emails
- ✅ Within the same organization, values must still be unique
- ✅ Better multi-tenancy support
- ✅ No cross-organization conflicts

## Example

**Before**: Only one organization could have a course with slug `"intro-to-programming"`

**After**: Every organization can have their own course with slug `"intro-to-programming"`

