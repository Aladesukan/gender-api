Gender Classification API (Laravel)

Overview

This project is a simple backend API built with Laravel that integrates with the Genderize API to classify a person's gender based on their name.

It was developed as part of the Backend Wizards Stage 0 Assessment, focusing on API integration, data processing, and proper error handling.


Features

1 Accepts a name as a query parameter
2 Calls an external API (Genderize)
3 Processes and structures the response
4 Computes confidence level based on defined rules
5 Handles errors and edge cases properly
6 Returns JSON responses in a consistent format


Endpoint

Classify Name

GET `/api/classify?name={name}`

Example:

/api/classify?name=john


Success Response

json
{
  "status": "success",
  "data": {
    "name": "john",
    "gender": "male",
    "probability": 0.99,
    "sample_size": 1234,
    "is_confident": true,
    "processed_at": "2026-04-01T12:00:00Z"
  }
}


Error Responses

Missing Name

json
{
  "status": "error",
  "message": "Missing name parameter"
}


Invalid Name Type

json
{
  "status": "error",
  "message": "Name must be a string"
}

No Prediction Available

json
{
  "status": "error",
  "message": "No prediction available for the provided name"
}

Server / External API Error

json
{
  "status": "error",
  "message": "Something went wrong"
}


How It Works

1. The API receives a request with a name parameter
2. It sends a GET request to the Genderize API
3. Extracts:

   * gender
   * probability
   * count (renamed to sample_size)
4. Applies confidence logic:

   * `is_confident = true` if:

     * probability ≥ 0.7
     * sample_size ≥ 100
5. Adds a timestamp (`processed_at`) in UTC format
6. Returns a structured JSON response


Confidence Logic

is_confident = (probability >= 0.7) AND (sample_size >= 100)


Tech Stack

PHP
Laravel
HTTP Client (Laravel)
External API: Genderize

Live API

https://gender-api-production-1077.up.railway.app

Testing

The API was tested using:

 Browser
 Postman
 Laravel local server

Challenges & Solutions

1. API Routes Not Loading in Deployment

Issue: `/api/classify` returned "Not Found" on Railway
Solution: Mapped API routes through `web.php` to ensure proper route resolution in the deployment environment

2. Deployment Failure Due to Database Configuration

Issue: Laravel attempted to use SQLite database which was not available
Solution: Switched to file-based cache and session configuration

3. Port Configuration Issue

Issue: Application failed to respond on Railway
Solution: Updated server to use dynamic `$PORT` instead of hardcoded port


Installation (Laragon)

git init
cd project-folder
composer install
php artisan install:api
php artisan serve

Author

Aladesukan Fiyinfoluwa

Contact

Email: [aladesukanf@gmail.com](mailto:aladesukanf@gmail.com)

Notes

This project focuses on backend fundamentals including:

1 API integration
2 Data transformation
3 Error handling
4 Deployment troubleshooting

Final Thoughts

This project demonstrates the ability to:

1 Build and structure APIs
2 Integrate third-party services
3 Debug real-world deployment issues
4 Deliver a production-ready backend solution
