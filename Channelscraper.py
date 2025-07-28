#!/usr/bin/env python3
"""
Simple YouTube Channel Scraper
Extracts video data from a YouTube channel and saves to CSV
"""
# I HAVE TO PUT IN THE /VIDEOS FOR IT TO WORK OTHERWISE IT WORK WORK, BUT IT WORKS WHEN I ADD THAT LIKE THIS https://www.youtube.com/@CodeBullet/videos


import csv
import subprocess
import sys
import json
from urllib.parse import urlparse, parse_qs

def install_requirements():
    """Install required packages"""
    try:
        import yt_dlp
    except ImportError:
        print("Installing yt-dlp...")
        subprocess.check_call([sys.executable, "-m", "pip", "install", "yt-dlp"])
        import yt_dlp

def extract_video_id(url):
    """Extract video ID from YouTube URL"""
    parsed = urlparse(url)
    if parsed.hostname == 'youtu.be':
        return parsed.path[1:]
    if parsed.hostname in ('www.youtube.com', 'youtube.com'):
        if parsed.path == '/watch':
            return parse_qs(parsed.query)['v'][0]
        if parsed.path[:7] == '/embed/':
            return parsed.path.split('/')[2]
        if parsed.path[:3] == '/v/':
            return parsed.path.split('/')[2]
    return None

def scrape_channel(channel_url, test_mode=False):
    """Scrape YouTube channel for video data"""
    install_requirements()
    import yt_dlp
    
    # Configure yt-dlp options
    ydl_opts = {
        'quiet': True,
        'no_warnings': True,
        'extract_flat': True,  # Don't download, just get metadata
    }
    
    if test_mode:
        ydl_opts['playlistend'] = 6  # Only get first 6 videos
    
    print(f"Scraping channel: {channel_url}")
    print("This may take a moment...")
    
    try:
        with yt_dlp.YoutubeDL(ydl_opts) as ydl:
            # Extract channel info
            info = ydl.extract_info(channel_url, download=False)
            
            if 'entries' not in info:
                print("Error: Could not find videos in this channel")
                return None
            
            videos = []
            channel_name = info.get('uploader', 'Unknown Channel')
            
            print(f"Found channel: {channel_name}")
            print(f"Processing {len(info['entries'])} videos...")
            
            for i, entry in enumerate(info['entries'], 1):
                if entry is None:
                    continue
                    
                video_data = {
                    'id': i,
                    'title': entry.get('title', 'Unknown Title'),
                    'youtube_id': entry.get('id', ''),
                    'subject': 'REPLACE_ME',  # User will fill this
                    'age': 'REPLACE_ME',      # User will fill this
                    'tools': 'REPLACE_ME',    # User will fill this
                    'cost': 'REPLACE_ME',     # User will fill this
                    'creator': channel_name
                }
                
                videos.append(video_data)
                print(f"Processed: {video_data['title']}")
            
            return videos
            
    except Exception as e:
        print(f"Error scraping channel: {str(e)}")
        return None

def save_to_csv(videos, filename):
    """Save video data to CSV file"""
    if not videos:
        print("No videos to save")
        return
    
    fieldnames = ['id', 'title', 'youtube_id', 'subject', 'age', 'tools', 'cost', 'creator']
    
    with open(filename, 'w', newline='', encoding='utf-8') as csvfile:
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(videos)
    
    print(f"Saved {len(videos)} videos to {filename}")

def main():
    """Main function"""
    print("=== YouTube Channel Scraper ===")
    print()
    
    # Get channel URL from user
    channel_url = input("Enter YouTube channel URL: ").strip()
    
    if not channel_url:
        print("Error: Please provide a channel URL")
        return
    
    # Ask if user wants test mode
    test_choice = input("Test mode (first 6 videos only)? (y/n): ").strip().lower()
    test_mode = test_choice == 'y'
    
    if test_mode:
        print("Running in TEST MODE - only first 6 videos")
    
    # Scrape the channel
    videos = scrape_channel(channel_url, test_mode)
    
    if videos:
        # Generate filename
        suffix = "_test" if test_mode else ""
        filename = f"youtube_videos{suffix}.csv"
        
        # Save to CSV
        save_to_csv(videos, filename)
        
        print()
        print("=== DONE ===")
        print(f"CSV file created: {filename}")
        print("Remember to replace 'REPLACE_ME' values with actual data!")
        
    else:
        print("Failed to scrape channel. Please check the URL and try again.")

if __name__ == "__main__":
    main()
