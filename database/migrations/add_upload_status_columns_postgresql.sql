-- Migration SQL for PostgreSQL
-- Add upload status columns to videos table
-- Run this SQL directly in your PostgreSQL database

-- Add upload_status column
ALTER TABLE videos 
ADD COLUMN IF NOT EXISTS upload_status VARCHAR(255) DEFAULT 'pending';

-- Add upload_progress column
ALTER TABLE videos 
ADD COLUMN IF NOT EXISTS upload_progress INTEGER DEFAULT 0;

-- Add upload_error column
ALTER TABLE videos 
ADD COLUMN IF NOT EXISTS upload_error TEXT;

-- Update existing videos that have video_url to 'completed' status
UPDATE videos 
SET upload_status = 'completed', upload_progress = 100 
WHERE upload_status = 'pending' AND video_url IS NOT NULL AND video_url != '';

-- Add comments for documentation
COMMENT ON COLUMN videos.upload_status IS 'Video upload status: pending, processing, completed, failed';
COMMENT ON COLUMN videos.upload_progress IS 'Upload progress percentage (0-100)';
COMMENT ON COLUMN videos.upload_error IS 'Error message if upload failed';

