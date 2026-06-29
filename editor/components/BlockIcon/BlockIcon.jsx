import React from 'react';

export default function BlockIcon() {
  const DAY = new Date().getDate(); 

  return (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
      <g fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4.25" width="14" height="12.75" rx="2.4"/>
        <path d="M3 8.25H17"/>
        <path d="M6.75 2.75V5.75"/>
        <path d="M13.25 2.75V5.75"/>
      </g>
      <text x="10" y="14.7" text-anchor="middle" font-family="sans-serif" font-size="7.4" font-weight="700" fill="currentColor">{DAY}</text>
    </svg>
  )
}
