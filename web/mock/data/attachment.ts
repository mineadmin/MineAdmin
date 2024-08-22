// data/resourceData.ts

function generateData() {
  return Array.from({ length: 100 }, (_, index) => {
    // 使用id作为基础来生成“随机”但一致的数据
    const id = index + 1
    const storageModes = [1, 2, 3]
    const suffixes = ['.txt', '.jpg', '.png', '.docx']

    return {
      id,
      storage_mode: storageModes[(id - 1) % storageModes.length],
      origin_name: `File_${id}`,
      object_name: `Object_${id}`,
      hash: `${id}`.padStart(10, '0'), // 示例：用id填充以生成固定长度的字符串
      mime_type: `type${id % 3 + 1}`,
      storage_path: `/path/to/resource_${id}`,
      suffix: suffixes[(id - 1) % suffixes.length],
      size_byte: 1024 * id, // 示例：基于id的简单计算
      size_info: `${id}KB`, // 直接基于id生成
      url: `http://example.com/resource_${id}`,
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
