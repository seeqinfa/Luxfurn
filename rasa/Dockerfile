FROM python:3.9-slim

# Set working directory
WORKDIR /app

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive
ENV PIP_NO_CACHE_DIR=1
ENV PIP_DISABLE_PIP_VERSION_CHECK=1

# Copy requirements first for better caching
COPY requirements.txt .

# Install Python dependencies without building from source
RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir --only-binary=all -r requirements.txt

# Copy Rasa project files
COPY . .

# Skip training in Docker build - do it at runtime if needed
# This avoids build timeouts

# Create a lightweight start script
RUN echo '#!/bin/bash\n\
set -e\n\
echo "Starting Rasa server..."\n\
# Try to train model if it doesnt exist, with timeout\n\
if [ ! -d "models" ] || [ -z "$(ls -A models)" ]; then\n\
  echo "No model found, training..."\n\
  timeout 300 rasa train || echo "Training skipped due to timeout"\n\
fi\n\
echo "Starting Rasa server on port ${PORT:-5005}"\n\
rasa run --enable-api --cors "*" --port ${PORT:-5005}\n\
' > /app/start.sh && chmod +x /app/start.sh

# Expose port
EXPOSE ${PORT:-5005}

# Start Rasa server
CMD ["/app/start.sh"]