export function buildEditUrl(postType: string, id: number) {
    return `/wp-admin/post.php?post=${id}&action=edit`;
}
