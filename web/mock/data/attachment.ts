// data/resourceData.ts

function generateData() {
  const storageModes = [1, 2, 3]
  const fileTypes = ['image', 'document', 'audio', 'video', 'package']
  const mimeTypes = {
    image: ['image/jpeg', 'image/png', 'image/gif'],
    document: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    audio: ['audio/mpeg', 'audio/wav'],
    video: ['video/mp4', 'video/avi'],
    package: ['application/zip', 'application/x-rar-compressed', 'application/x-gzip'],
  }
  const suffixes = {
    image: ['jpg', 'png', 'gif'],
    document: ['pdf', 'doc', 'docx'],
    audio: ['mp3', 'wav'],
    video: ['mp4', 'avi'],
    package: ['zip', 'rar', 'gz'],
  }
  return Array.from({ length: 100 }, (_, index) => {
    // 使用id作为基础来生成“随机”但一致的数据
    const id = index + 1
    const fileType = fileTypes[(id - 1) % fileTypes.length]
    const mime_type = mimeTypes[fileType][(id - 1) % mimeTypes[fileType].length]
    const suffix = suffixes[fileType][(id - 1) % suffixes[fileType].length]
    return {
      id,
      storage_mode: storageModes[(id - 1) % storageModes.length],
      origin_name: `中国大熊猫${id}.${suffix}`,
      object_name: `Object${12345678 * id}`,
      hash: `${id}`.padStart(10, '0'), // 示例：用id填充以生成固定长度的字符串
      mime_type,
      storage_path: `/path/to/resource_${id}`,
      suffix,
      size_byte: 1024 * id, // 示例：基于id的简单计算
      size_info: `${id}KB`, // 直接基于id生成
      url: `https://picsum.photos/300/300?random=${id}`,
      created_by: (id % 10) + 1,
      updated_by: (id % 10) + 1,
      created_at: `2023-01-01`,
      updated_at: `2023-01-01`,
      deleted_at: null,
      remark: `This is a remark for id ${id}.`,
    }
  })
}

export const attachments = generateData()
