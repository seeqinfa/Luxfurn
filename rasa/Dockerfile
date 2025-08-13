FROM python:3.9-slim

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    gcc \
    g++ \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements first for better caching
COPY requirements.txt .

# Install Python dependencies
RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir -r requirements.txt

# Copy Rasa project files
COPY . .

# Train the model
RUN rasa train

# Create actions server start script
RUN echo '#!/bin/bash\nrasa run actions &\nrasa run --enable-api --cors "*" --port $PORT' > start.sh && \
    chmod +x start.sh

# Expose port (Railway will set PORT environment variable)
EXPOSE $PORT

# Start both actions server and main Rasa server
CMD ["./start.sh"]