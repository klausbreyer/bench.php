<?php
/**
 * PHP Benchmark Script for the Command Line with Plain Text Output
 *
 * This script benchmarks the CPU, memory, and IO performance of the server.
 * It provides real-time progress updates and formats the results for easy comparison.
 */

/**
 * Get the current time in microseconds.
 *
 * @return float Current time in seconds with microseconds.
 */
function getTime()
{
	return microtime(true);
}

/**
 * Immediately output and flush the buffer.
 */
function flushOutput()
{
	if (ob_get_level() > 0) {
		ob_end_flush();
	}
	flush();
}

/**
 * CPU Benchmark: Calculate the Fibonacci sequence recursively.
 *
 * @param int $iterations The Fibonacci number to calculate.
 * @return array The result and time taken in milliseconds.
 */
function cpuBenchmark($iterations = 30)
{
	echo "Starting CPU Benchmark...\n";
	flushOutput();

	/**
	 * Recursive function to calculate Fibonacci numbers.
	 *
	 * @param int $n The position in the Fibonacci sequence.
	 * @return int The Fibonacci number at position $n.
	 */
	function fibonacci($n)
	{
		if ($n <= 1)
			return $n;
		return fibonacci($n - 1) + fibonacci($n - 2);
	}

	$start = getTime();
	$result = fibonacci($iterations);
	$end = getTime();

	$duration_ms = ($end - $start) * 1000;

	echo "CPU Benchmark Completed.\n";
	flushOutput();

	return [
		'result' => $result,
		'time_ms' => $duration_ms
	];
}

/**
 * Memory Benchmark: Create and manipulate a large array with progress updates.
 *
 * @return array The peak memory used in MB and the time taken in milliseconds.
 */
function memoryBenchmark()
{
	echo "Starting Memory Benchmark...\n";
	flushOutput();

	// Force garbage collection to clear memory
	gc_collect_cycles();

	$startMemory = memory_get_peak_usage(true);
	$startTime = getTime();

	// Create a large array with progress updates
	$array = [];
	$totalIterations = 100000;
	$progressInterval = 20000; // Update every 20,000 iterations

	for ($i = 0; $i < $totalIterations; $i++) {
		$array[] = md5($i);

		// Provide progress updates
		if (($i + 1) % $progressInterval == 0) {
			echo "  - Memory Benchmark Progress: " . ($i + 1) . "/{$totalIterations} items created.\n";
			flushOutput();
		}
	}

	echo "Sorting the array...\n";
	flushOutput();

	// Manipulate the array
	sort($array);

	$endTime = getTime();
	$endMemory = memory_get_peak_usage(true);

	$memory_used_mb = ($endMemory - $startMemory) / (1024 * 1024);
	$duration_ms = ($endTime - $startTime) * 1000;

	echo "Memory Benchmark Completed.\n";
	flushOutput();

	return [
		'memory_used_mb' => $memory_used_mb,
		'time_ms' => $duration_ms
	];
}

/**
 * IO Benchmark: Write and read a large file with progress updates.
 *
 * @param string $filename The name of the temporary file.
 * @return array Bytes written, write time, and read time in milliseconds.
 */
function ioBenchmark($filename = 'benchmark_test_file.tmp')
{
	echo "Starting IO Benchmark...\n";
	flushOutput();

	$data = str_repeat("The quick brown fox jumps over the lazy dog.\n", 10000); // approx. 450KB

	// Write to file
	echo "  - Writing data to file...\n";
	flushOutput();
	$startWrite = getTime();
	$bytesWritten = file_put_contents($filename, $data);
	$endWrite = getTime();
	$write_duration_ms = ($endWrite - $startWrite) * 1000;
	echo "  - Data written successfully.\n";
	flushOutput();

	// Read from file
	echo "  - Reading data from file...\n";
	flushOutput();
	$startRead = getTime();
	$readData = file_get_contents($filename);
	$endRead = getTime();
	$read_duration_ms = ($endRead - $startRead) * 1000;
	echo "  - Data read successfully.\n";
	flushOutput();

	// Delete the file
	echo "  - Deleting temporary file...\n";
	flushOutput();
	unlink($filename);
	echo "  - Temporary file deleted.\n";
	flushOutput();

	echo "IO Benchmark Completed.\n";
	flushOutput();

	return [
		'bytes_written' => $bytesWritten,
		'write_time_ms' => $write_duration_ms,
		'read_time_ms' => $read_duration_ms
	];
}

// Execute the benchmarks with progress updates
echo "PHP Benchmark Results\n";
echo "=====================\n\n";
flushOutput();

$cpu = cpuBenchmark(30); // Adjusted to 30 iterations
echo "\n";

$memory = memoryBenchmark();
echo "\n";

$io = ioBenchmark();
echo "\n";

// Summary of the results in plain text
echo "Summary\n";
echo "=======\n";
echo "CPU Benchmark:\n";
echo "  - Fibonacci(30) = {$cpu['result']}\n";
echo "  - Duration: " . number_format($cpu['time_ms'], 2) . " ms\n\n";

echo "Memory Benchmark:\n";
echo "  - Peak Memory Used: " . number_format($memory['memory_used_mb'], 2) . " MB\n";
echo "  - Duration: " . number_format($memory['time_ms'], 2) . " ms\n\n";

echo "IO Benchmark:\n";
echo "  - Bytes Written: {$io['bytes_written']} bytes\n";
echo "  - Write Duration: " . number_format($io['write_time_ms'], 2) . " ms\n";
echo "  - Read Duration: " . number_format($io['read_time_ms'], 2) . " ms\n";
