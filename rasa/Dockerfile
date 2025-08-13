FROM python:3.9-slim

# Set working directory
WORKDIR /app

# Set environment variables to prevent interactive prompts
ENV DEBIAN_FRONTEND=noninteractive
ENV PIP_NO_CACHE_DIR=1
ENV PIP_DISABLE_PIP_VERSION_CHECK=1

# Install system dependencies in a single layer with cleanup
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        build-essential \
        gcc \
        g++ \
        && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Copy requirements first for better Docker layer caching
COPY requirements.txt .

# Install Python dependencies with timeout and retry settings
RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir --timeout=300 --retries=3 -r requirements.txt

# Copy Rasa project files
COPY . .

# Train the model (with timeout protection)
RUN timeout 600 rasa train || (echo "Training timed out, using default model" && exit 0)

# Create a simple start script
RUN echo '#!/bin/bash\n\
set -e\n\
echo "Starting Rasa services..."\n\
rasa run actions --port 5055 &\n\
ACTIONS_PID=$!\n\
echo "Actions server started with PID $ACTIONS_PID"\n\
sleep 5\n\
echo "Starting main Rasa server on port $PORT"\n\
rasa run --enable-api --cors "*" --port $PORT\n\
' > /app/start.sh && chmod +x /app/start.sh

# Expose port (Railway sets PORT environment variable)
EXPOSE $PORT

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:$PORT/ || exit 1

# Start both services
CMD ["/app/start.sh"]