<?php
/**
 * Pagination Helper Functions
 * Provides reusable pagination logic for property listings
 */

/**
 * Calculate pagination values
 * @param int $currentPage Current page number
 * @param int $itemsPerPage Number of items per page
 * @return array Array with 'offset' and 'limit' values
 */
function getPaginationValues($currentPage = 1, $itemsPerPage = 12) {
    $currentPage = max(1, (int)$currentPage);
    $itemsPerPage = max(1, (int)$itemsPerPage);
    
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'page' => $currentPage,
        'items_per_page' => $itemsPerPage,
        'offset' => $offset,
        'limit' => $itemsPerPage
    ];
}

/**
 * Get total number of pages
 * @param int $totalItems Total number of items
 * @param int $itemsPerPage Number of items per page
 * @return int Total number of pages
 */
function getTotalPages($totalItems, $itemsPerPage = 12) {
    return max(1, ceil($totalItems / $itemsPerPage));
}

/**
 * Generate pagination HTML
 * @param int $currentPage Current page number
 * @param int $totalPages Total number of pages
 * @param string $baseUrl Base URL for pagination links (without page parameter)
 * @param array $additionalParams Additional URL parameters to preserve
 * @return string HTML for pagination controls
 */
function generatePagination($currentPage, $totalPages, $baseUrl, $additionalParams = []) {
    if ($totalPages <= 1) {
        return '';
    }
    
    // Build query string for additional parameters
    $queryString = '';
    if (!empty($additionalParams)) {
        $queryParts = [];
        foreach ($additionalParams as $key => $value) {
            if ($key !== 'page' && !empty($value)) {
                $queryParts[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        if (!empty($queryParts)) {
            $queryString = '&' . implode('&', $queryParts);
        }
    }
    
    $html = '<div class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $html .= '<a href="' . htmlspecialchars($baseUrl . '?page=' . $prevPage . $queryString) . '" class="pagination-btn pagination-prev" title="' . t('previous_page') . '">';
        $html .= '<i class="fas fa-chevron-left"></i> ' . t('previous');
        $html .= '</a>';
    } else {
        $html .= '<span class="pagination-btn pagination-prev disabled">';
        $html .= '<i class="fas fa-chevron-left"></i> ' . t('previous');
        $html .= '</span>';
    }
    
    // Page numbers
    $html .= '<div class="pagination-numbers">';
    
    // Show first page
    if ($currentPage > 3) {
        $html .= '<a href="' . htmlspecialchars($baseUrl . '?page=1' . $queryString) . '" class="pagination-number">1</a>';
        if ($currentPage > 4) {
            $html .= '<span class="pagination-ellipsis">...</span>';
        }
    }
    
    // Show pages around current page
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="pagination-number active">' . $i . '</span>';
        } else {
            $html .= '<a href="' . htmlspecialchars($baseUrl . '?page=' . $i . $queryString) . '" class="pagination-number">' . $i . '</a>';
        }
    }
    
    // Show last page
    if ($currentPage < $totalPages - 2) {
        if ($currentPage < $totalPages - 3) {
            $html .= '<span class="pagination-ellipsis">...</span>';
        }
        $html .= '<a href="' . htmlspecialchars($baseUrl . '?page=' . $totalPages . $queryString) . '" class="pagination-number">' . $totalPages . '</a>';
    }
    
    $html .= '</div>';
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $html .= '<a href="' . htmlspecialchars($baseUrl . '?page=' . $nextPage . $queryString) . '" class="pagination-btn pagination-next" title="' . t('next_page') . '">';
        $html .= t('next') . ' <i class="fas fa-chevron-right"></i>';
        $html .= '</a>';
    } else {
        $html .= '<span class="pagination-btn pagination-next disabled">';
        $html .= t('next') . ' <i class="fas fa-chevron-right"></i>';
        $html .= '</span>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Get count query for pagination
 * Takes the main query and converts it to a COUNT query
 * @param string $mainQuery The main SELECT query
 * @return string COUNT query
 */
function getCountQuery($mainQuery) {
    // Extract the FROM clause and everything after it (WHERE, GROUP BY, etc.)
    // but remove ORDER BY and LIMIT
    $countQuery = preg_replace('/SELECT\s+.*?\s+FROM/i', 'SELECT COUNT(*) as total FROM', $mainQuery);
    
    // Remove ORDER BY clause and everything after it
    $countQuery = preg_replace('/\s+ORDER\s+BY\s+.*$/i', '', $countQuery);
    
    // Remove LIMIT clause
    $countQuery = preg_replace('/\s+LIMIT\s+.*$/i', '', $countQuery);
    
    // Remove OFFSET clause
    $countQuery = preg_replace('/\s+OFFSET\s+.*$/i', '', $countQuery);
    
    // Handle edge case where query might have issues
    if (empty($countQuery)) {
        return "SELECT COUNT(*) as total FROM properties";
    }
    
    return trim($countQuery);
}
?>